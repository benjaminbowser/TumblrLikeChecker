# Tumblr Like Checker

Tumblr Like Checker is designed for Taylor Swift fans to easily search through her 40,000 likes by entering a blog name to see which posts of theirs she 
has liked. It is designed to compliment websites like [IsTayOntumblr](http://istayontumblr.com/) and the list of her likes on 
[Tumblr.com](https://www.tumblr.com/liked/by/taylorswift). It is not designed to replace any features that are available on the tumblr website.

## Installation

Create a MySQL database on your server and fill in the credentials on the getLikes.php and rest.php files. Ensure that your database is accessible to 
wherever you'll be using these files. If the database can only be accessed on localhost, you will want to put these files on that same server.

Next, you will need to install [Guzzle](http://docs.guzzlephp.org/en/stable/overview.html). 

In order to fill the database with all of the likes, you will need to generate a [Tumblr API Key](https://www.tumblr.com/docs/en/api/v2) and put it in the 
getLikes.php file in the few places the API key is needed.

From a console, you will want to run the getLikes.php file (do not go to this file in a browser). You can put this file on the server in a spot that 
doesn't face the public internet to prevent bad intentions.

```bash
php getLikes.php
```

This script will continue to make calls to tumblr and store them in the database until it has all of them. Once you get near the end, you may need to self 
terminate the script. The script still needs a little work. Please make sure that the API key you use has enough calls left for the day to fill the 
database (~1000 calls). As more posts are liked, simply run the script for a few minutes, or until you see the number of new posts you want to get, and 
then self stop the script.

The database will not have the full like count of posts at the end, this is because the like count on tumblr accounts for posts that have been deleted or 
from accounts that have been deleted.


