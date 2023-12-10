# antispam-per-session-email-displayer
This is a php script that creates a custom email address on the fly, so every user sees a temporary but persistent contact email address on their website that's (mostly) unique to them as an anti-spam meause.

It's an age-old problem: you want to display your email address on your site, but if you do, spammers will scrape it and spam you.

On my site https://kupietz.com, everywhere I need to give my email address, even in `<a href="mailto:xxx@yyy.zzz">` tags, I include this php script (in my case I'm using a wordpress plugin that allows embedding PHP with a shortcode.)

The script generates a per-session email address based on the user's browser info, IP address, and some other session info. That way the email stays unchanged through reloads, on different pages of the site, etc. It's stored per session but using the user's info means they can come back tomorrow with a new session and still get the same email address. I do have it set to change weekly just to make sure they rotate occasionally.

Then, if a spammer crawls my site and grabs my email address, instead of getting `myrealemailaddress@kupietz.com`, they get something like `fmconsulting-e55@kupietz.com`. Then when I start getting spammed to death on `fmconsulting-e55@kupietz.com`, I can just block that  one address, and my main email address is left unmolested.

**WARNINGS, IMPORTANT:** 

1. This script generates logs every time it creates a new email address for a visitor, with the name `genEmail_blahblahblah.log`, in my website's main directory. These need to be periodically cleared out manually. These are because when I get spammed on an address, I have a morbid curiosity to look in the logs and see the info of the visitor the address was provided to. 

2. Obviously in this day and age you don't want to use a catchall email address (well, at least, I don't.) So you have to have some way of accepting all the various combinations of addresses this might generate, without leaving your email wide open with a catchall. I have a way of doing this for myself, but for security reasons, that one has to remain private. I would suggest, if you don't want to use a catchall, having some sort of filter that accepts any email addressed to one of the prefixes specified in the script from which it generates the first part of the script, which are specified in the user options section at top.

# I am
Michael E. Kupietz, software engineering, consulting, & support for FileMaker Pro, Full-Stack Web, Desktop OS, & TradingView platforms  
https://kupietz.com (Business info)  
https://github.com/kupietools (Free software)  
https://michaelkupietz.com (Personal & creative showcase)  

