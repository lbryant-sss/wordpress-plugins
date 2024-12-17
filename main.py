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
MAX_PLUGINS = 1000

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


def get_all_plugin_slugs():
    """
    Fetch a comprehensive list of WordPress plugin slugs.
    
    Note: This is a simplified approach and may not get ALL 80,000+ plugins.
    WordPress API has limitations on bulk retrieval.
    """
    plugin_slugs = []
    page = 1
    per_page = 250  # WordPress.org API typically allows around this many per request

    while True:
        try:
            # WordPress.org browse API endpoint
            browse_url = f"https://api.wordpress.org/plugins/info/1.2/?action=query_plugins&request[page]={page}&request[per_page]={per_page}&request[browse]=popular"
            
            response = requests.get(browse_url)
            if response.status_code != 200:
                print(f"Failed to fetch plugins on page {page}. Status code: {response.status_code}")
                break

            data = response.json()
            
            # Extract plugin slugs
            page_plugins = [plugin['slug'] for plugin in data.get('plugins', [])]
            
            # Add to our list
            plugin_slugs.extend(page_plugins)
            
            # Break if no more plugins or reached max limit
            if len(page_plugins) < per_page or len(plugin_slugs) >= MAX_PLUGINS:
                break
            
            page += 1
            
            # Respect rate limits
            time.sleep(TIME_INTERVAL)

        except Exception as e:
            print(f"Error fetching plugins: {e}")
            break

    return plugin_slugs[:MAX_PLUGINS]

def download_plugin(plugin_slug, dest_dir):
    """Download and extract a WordPress plugin."""
    print(f"Downloading plugin: {plugin_slug}")
    try:
        response = requests.get(f"{DOWNLOAD_URL}{plugin_slug}.zip", stream=True, timeout=30)
        
        if response.status_code == 200:
            zip_path = os.path.join(dest_dir, f"{plugin_slug}.zip")
            with open(zip_path, "wb") as file:
                file.write(response.content)
            
            with zipfile.ZipFile(zip_path, "r") as zip_ref:
                zip_ref.extractall(dest_dir)
            
            os.remove(zip_path)
            print(f"Downloaded and extracted {plugin_slug}.")
            return True
        else:
            print(f"Failed to download {plugin_slug} (Status: {response.status_code}).")
            return False
    
    except requests.RequestException as e:
        print(f"Download error for {plugin_slug}: {e}")
        return False

def update_plugins(repo_dir, plugin_list):
    """Update plugins in the local repository."""
    plugins_dir = os.path.join(repo_dir, "plugins")
    
    # Create plugins directory if not exists
    os.makedirs(plugins_dir, exist_ok=True)
    
    # Track successful and failed downloads
    successful_downloads = []
    failed_downloads = []
    
    for plugin_slug in plugin_list:
        try:
            plugin_path = os.path.join(plugins_dir, plugin_slug)
            
            # Remove old plugin folder if exists
            if os.path.exists(plugin_path):
                shutil.rmtree(plugin_path)
            
            # Attempt to download plugin
            if download_plugin(plugin_slug, plugins_dir):
                successful_downloads.append(plugin_slug)
            else:
                failed_downloads.append(plugin_slug)
            
            # Respect rate limits
            time.sleep(TIME_INTERVAL)
        
        except Exception as e:
            print(f"Error processing plugin {plugin_slug}: {e}")
            failed_downloads.append(plugin_slug)
    
    # Commit changes to the repository
    try:
        repo = Repo(repo_dir)
        repo.git.add(A=True)
        
        if repo.is_dirty():
            print("Changes detected. Preparing to commit...")
            
            # Configure git user for the commit
            with repo.config_writer() as git_config:
                git_config.set_value("user", "name", "GitHub Actions Bot")
                git_config.set_value("user", "email", "actions@github.com")
            
            # Commit changes
            commit_message = f"Download {len(successful_downloads)} WordPress plugins"
            repo.index.commit(commit_message)
            
            print("Committing changes...")
            
            # Push changes
            try:
                origin = repo.remote(name='origin')
                push_result = origin.push()
                print("Successfully pushed changes.")
            
            except Exception as push_error:
                print(f"Error during push: {push_error}")
    
    except Exception as repo_error:
        print(f"Repository error: {repo_error}")
    
    # Log download results
    print(f"Successfully downloaded {len(successful_downloads)} plugins")
    print(f"Failed to download {len(failed_downloads)} plugins")
    if failed_downloads:
        print("Failed plugins:", failed_downloads)

def main():
    """Main script execution."""
    print("Checking if repository exists...")

    # Ensure REPO_DIR exists and is empty before cloning
    if os.path.exists(REPO_DIR):
        shutil.rmtree(REPO_DIR)
    
    print("Cloning repository...")
    Repo.clone_from(REPO_URL, REPO_DIR)

    print("Fetching plugin list...")
    plugin_list = get_all_plugin_slugs()
    
    print(f"Found {len(plugin_list)} plugins to download")
    
    print("Downloading plugins...")
    update_plugins(REPO_DIR, plugin_list)

if __name__ == "__main__":
    main()












