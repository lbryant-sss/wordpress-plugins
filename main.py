import os
import json
import time
import requests
import zipfile
import shutil
from git import Repo

<<<<<<< HEAD
=======

REPO_URL = "git@github.com/lbryant-sss/wordpress-plugins.git"
REPO_DIR = './repo'

# Clone the repository
Repo.clone_from(REPO_URL, REPO_DIR)


>>>>>>> a1fb748a222b63fb909a48724914bff17f8cc178
# Configuration
REPO_URL = "git@github.com:lbryant-sss/wordpress-plugins.git"  # SSH URL
REPO_DIR = './repo'
CACHE_FILE = "cache.json"
WORDPRESS_API_URL = "https://api.wordpress.org/plugins/info/1.2/"
DOWNLOAD_URL = "https://downloads.wordpress.org/plugin/"
TIME_INTERVAL = 1  # Interval between requests in seconds

def load_cache():
    """Load plugin cache file or create a new one."""
    if os.path.exists(CACHE_FILE):
        with open(CACHE_FILE, "r") as file:
            return json.load(file)
    return {"timestamp": 0, "plugins": {}}

def save_cache(cache):
    """Save the plugin cache."""
    with open(CACHE_FILE, "w") as file:
        json.dump(cache, file)

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

    for plugin_slug in plugin_list:
        print(f"Processing plugin: {plugin_slug}")
        response = requests.get(f"{WORDPRESS_API_URL}?action=plugin_information&request[slug]={plugin_slug}")
        
        # Check for rate limits in the response headers
        if response.status_code == 429:  # Too many requests
            reset_time = int(response.headers.get('X-RateLimit-Reset', time.time() + 60))  # Retry after reset time
            wait_time = reset_time - int(time.time()) + 1  # Add 1 second buffer
            print(f"Rate limit exceeded. Waiting for {wait_time} seconds...")
            time.sleep(wait_time)
            continue  # Retry after sleeping

        if response.status_code != 200:
            print(f"Failed to fetch plugin info for {plugin_slug}. Skipping...")
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

        # Respect rate limits
        time.sleep(TIME_INTERVAL)

    # Commit changes to the repository
    repo = Repo(repo_dir)
    repo.git.add(A=True)
    if repo.is_dirty():
        print("Committing changes...")
        repo.index.commit("Update WordPress plugins")
        print("Pushing changes...")
        repo.remotes.origin.push()
    else:
        print("No changes to commit.")

def main():
    """Main script execution."""
    print("Checking if repository exists...")

    # Clone the repository if it doesn't exist yet
    if not os.path.exists(REPO_DIR):
        print("Cloning repository...")
        Repo.clone_from(REPO_URL, REPO_DIR)

    print("Updating plugins...")
    plugin_list = get_plugin_list()
    update_plugins(REPO_DIR, plugin_list)

if __name__ == "__main__":
    main()
