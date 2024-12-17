import os
import json
import time
import requests
import zipfile
import shutil
from git import Repo

# Use environment variable for authentication
GITHUB_TOKEN = os.environ.get('GH_PAT')
REPO_URL = f"https://oauth2:{GITHUB_TOKEN}@github.com/lbryant-sss/wordpress-plugins.git"

# Configuration
REPO_DIR = './repo'
CACHE_FILE = "cache.json"

# Ensure cache directory exists
os.makedirs(os.path.dirname(CACHE_FILE) or '.', exist_ok=True)

WORDPRESS_API_URL = "https://api.wordpress.org/plugins/info/1.2/"
DOWNLOAD_URL = "https://downloads.wordpress.org/plugin/"
TIME_INTERVAL = 1  # Interval between requests in seconds

def load_cache():
    """Load plugin cache file or create a new one with default structure."""
    try:
        if not os.path.exists(CACHE_FILE):
            return {"timestamp": 0, "plugins": {}}
        
        with open(CACHE_FILE, "r") as file:
            content = file.read().strip()
            
            # If file is empty or malformed, return default cache
            if not content or content == '{}':
                return {"timestamp": 0, "plugins": {}}
            
            # Try to parse JSON
            return json.loads(content)
    
    except (json.JSONDecodeError, IOError) as e:
        print(f"Error reading cache file: {e}")
        # Create a fresh, valid cache file
        default_cache = {"timestamp": 0, "plugins": {}}
        save_cache(default_cache)
        return default_cache

def save_cache(cache):
    """Save the plugin cache, ensuring proper JSON formatting."""
    try:
        with open(CACHE_FILE, "w") as file:
            json.dump(cache, file, indent=2)  # Added indent for readability
    except IOError as e:
        print(f"Error saving cache file: {e}")

def get_plugin_list():
    """Return a static list of plugins or fetch dynamically."""
    return ["woocommerce", "akismet", "jetpack"]  # Example plugins

def download_plugin(plugin_slug, dest_dir):
    """Download and extract a WordPress plugin."""
    print(f"Downloading plugin: {plugin_slug}")
    response = requests.get(f"{DOWNLOAD_URL}{plugin_slug}.zip", stream=True)
    if response.status_code == 200:
        zip_path = os.path.join(dest_dir, f"{plugin_slug}.zip")
        with open(zip_path, "wb") as file:
            file.write(response.content)
        with zipfile.ZipFile(zip_path, "r") as zip_ref:
            zip_ref.extractall(dest_dir)
        os.remove(zip_path)
        print(f"Downloaded and extracted {plugin_slug}.")
    else:
        print(f"Failed to download {plugin_slug} (Status: {response.status_code}).")

def update_plugins(repo_dir, plugin_list):
    """Update plugins in the local repository."""
    cache = load_cache()
    plugins_dir = os.path.join(repo_dir, "plugins")
    
    # Create plugins directory if not exists
    os.makedirs(plugins_dir, exist_ok=True)
    
    # Track if any changes were made
    changes_made = False
    
    for plugin_slug in plugin_list:
        print(f"Processing plugin: {plugin_slug}")
        try:
            response = requests.get(f"{WORDPRESS_API_URL}?action=plugin_information&request[slug]={plugin_slug}")
            
            # Check for rate limits in the response headers
            if response.status_code == 429:  # Too many requests
                reset_time = int(response.headers.get('X-RateLimit-Reset', time.time() + 60))  # Retry after reset time
                wait_time = reset_time - int(time.time()) + 1  # Add 1 second buffer
                print(f"Rate limit exceeded. Waiting for {wait_time} seconds...")
                time.sleep(wait_time)
                continue  # Retry after sleeping
            
            if response.status_code != 200:
                print(f"Failed to fetch plugin info for {plugin_slug}. Skipping... (Status code: {response.status_code})")
                continue
            
            plugin_data = response.json()
            latest_version = plugin_data.get("version")
            plugin_path = os.path.join(plugins_dir, plugin_slug)
            
            # Skip download if version is up-to-date
            if cache["plugins"].get(plugin_slug) == latest_version:
                print(f"{plugin_slug} is already up-to-date.")
                continue
            
            # Remove old plugin folder and download the latest version
            if os.path.exists(plugin_path):
                shutil.rmtree(plugin_path)
            
            download_plugin(plugin_slug, plugins_dir)
            
            # Update cache
            cache["plugins"][plugin_slug] = latest_version
            save_cache(cache)
            
            # Mark that changes were made
            changes_made = True
            
            # Respect rate limits
            time.sleep(TIME_INTERVAL)
        
        except Exception as e:
            print(f"Error processing plugin {plugin_slug}: {e}")
            continue
    
    # Commit changes to the repository
    try:
        repo = Repo(repo_dir)
        
        # Stage all changes
        repo.git.add(A=True)
        
        # Check if there are any changes to commit
        if changes_made and repo.is_dirty():
            print("Changes detected. Preparing to commit...")
            
            # Configure git user for the commit
            with repo.config_writer() as git_config:
                git_config.set_value("user", "name", "GitHub Actions Bot")
                git_config.set_value("user", "email", "actions@github.com")
            
            # Commit changes
            commit_message = f"Update WordPress plugins: {', '.join(plugin_list)}"
            repo.index.commit(commit_message)
            
            print("Committing changes...")
            
            # Push changes with more robust error handling
            try:
                origin = repo.remote(name='origin')
                push_result = origin.push()
                
                # Check push result
                for info in push_result:
                    if info.flags & info.ERROR:
                        print(f"Push failed: {info.summary}")
                        raise Exception(f"Git push error: {info.summary}")
                
                print("Successfully pushed changes.")
            
            except Exception as push_error:
                print(f"Error during push: {push_error}")
                # Optionally, you could re-raise the exception if you want the action to fail
                # raise
        
        else:
            print("No changes to commit.")
    
    except Exception as repo_error:
        print(f"Repository error: {repo_error}")
        # Optionally, you could re-raise the exception if you want the action to fail
        # raise

    return changes_made

def main():
    """Main script execution."""
    print("Checking if repository exists...")

    # Ensure REPO_DIR exists and is empty before cloning
    if os.path.exists(REPO_DIR):
        shutil.rmtree(REPO_DIR)
    
    print("Cloning repository...")
    Repo.clone_from(REPO_URL, REPO_DIR)

    print("Updating plugins...")
    plugin_list = get_plugin_list()
    update_plugins(REPO_DIR, plugin_list)

if __name__ == "__main__":
    main()
