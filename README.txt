HOW TO DO STUFF WITH THE WARRIORSBAND.COM CODE

Table of Contents:

  1. Notes
  2. How to SSH into the webserver
  3. Cloning the github repository for the first time
  4. Making a change to the website
  5. Things to look into



1. NOTES

  * If you don't know the password(s) to the Github or webserver accounts, you'll
    have to find out from someone.
  * This README assumes you're on a Linux machine; if not, you'll have to figure out
    equivalent steps for Windows/other yourself.



2. HOW TO SSH INTO THE WEBSERVER

  You won't need to do this on a regular basis, but it's a good thing to know how to.
  The webserver is located in Steve Hayman's (a former CCWB) house, and he's paying
  for the server/hosting, so keep that in mind. He also offers no service guarantee;
  if the site's down for a significant amount of time you can contact him, but he's
  not running tech support for us.

  1. Run "ssh warriorsband@warriorsband.com".
  2. Provide the password.
  3. Poke around. Important directories are:
    * pear: local Pear installation, used for sending Gmail emails. Shouldn't need
      to be touched.
    * warriorsband.com: a symlink to the website directory. What's in here is what's
      on the website.
    * warriorsband.com-reunion_files: contains a website used for one of the band
      reunions. Don't need to touch it.
    * warriorsband.com_old: contains the contents of the old website. I suppose it
      can be deleted if we're sure we have a copy of all the images and other
      worthwhile content within.
  3. Run "mysql -p", enter the password (same as the warriorsband@warriorsband.com
     password), and type "use warriorsband_db".
  4. Poke around the database.



3. CLONING THE GITHUB REPOSITORY FOR THE FIRST TIME

  1. Follow the instructions at  https://help.github.com/articles/set-up-git  to get
     git set up
  2. Make a directory on your machine where you want to work on website code; for
     example, /home/you/code".
  3. Run "git clone https://github.com/warriorsband/warriorsband.com.git"
  4. Read a git tutorial to learn the fundamentals if you're confused. You should
     know about git commit, checkout, branch, push and pull at the very least.
  5. Copy config/config.txt to config/config.php, replacing YOUREMAIL and
     YOURPASSWORD with the band email and email password appropriately. I did this so
     that the password would not be publicly visible; sorry.



4. MAKING A CHANGE TO THE WEBSITE

  This is the general process you should follow when making a change to the website:

  1. Switch to the "devel" branch (or fork a new branch). You always want the master
     branch to be a functional version of the website, so you should work on devel
     and then merge those changes into master when you're sure it works.
  2. Make your changes, making sure to commit distinct changes along the way with
     "git commit", and push those changes up to github with "git push" in case your
     files get deleted locally or something.
  3. Test along the way by running "./deploy_working" (which uploads your working
     directory directly to the webserver) or "./deploy_devel" (which uploads the
     most recent devel branch to the webserver).
  4. When necessary, run "./deploy_release" to upload the most recent master branch
     contents to the webserver, effectively undoing whatever madness you wrought.
     In the interest of keeping the site working, don't leave broken code on it;
     roll back to working code if you tested, it didn't work, and it'll be a few more
     minutes before you fix it.
  5. Make sure of the following:
       * If you've modified, added or deleted a database table, modify tables.sql
         to reflect that change, so that we can re-create the database if it
         disappears somehow.
       * That no passwords or other secret info is in tracked files, since they will
         be publicly viewable on git.
  6. Merge your [presumably flawless] changes into master by running
     "git checkout master" followed by "git merge devel".



5. THINGS TO LOOK INTO

  * It's probably better for us to have a CS Club account and host the site there,
    and ask Steve Hayman (who also owns the warriorsband.com domain) to redirect it
    there.
  * We probably should do backups of the database...
