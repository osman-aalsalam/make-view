# make-view
make view is a laravel artisan command to make new view.

# to install the command
  1. create a new laravel 5.5 project.
  2. execute the artisan make:command name the command with MakeView name as bellow:
    ``php artisan make:command MakeView``
  3. goto app/console/commands directory.
  4. replace the file MakeView.php with our file here.
  5. enjoy the command.
  6. for help execute command with help:
    ``php artisan help make:view``

# How to use the command:
  make:view is a laravel artisan command to make a new view the command accept the path as argument where the view must be located in and other options related to view it self.
    - path: is view path location path.
    -# Options:
      - ``--extends=layouts.app``
        this option specify if the view must extend from master layouts or not if so the view will extend the layout after the equal sign, and if not the extends option must be removed from the command. the extends can be used multiple times in the command if the view has to extends from multiple master layout for example:
        ``php artisan make:view viewDir/view1.blade.php --extends=layouts.app1 --extends=layouts.app2``
        in this case the view1 view will be created when the command execute and the view will extends from app1 and app2 at the same time.
      - ``--section=sectionName``
        this option specify if the view has section from the layout or not if so the section option must appear in the command for example:
        ``php artisan make:view --extends=layouts.app --section=header --section=contents --section=footer``
        
        
