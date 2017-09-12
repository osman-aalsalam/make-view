<?php
/**
 * make:view - artisan command to create a view.
 * @author Osman Abdelsalam <http:osmanabdelsalam.epizy.com, https://github.com/osman-aalsalam/make-view/, developer.osmanabdelsalam@gmail.com>
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;

class makeView extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = '
                make:view {path : A path where the view will be added} 
                          {--e|extends=* : extend blade commands, you can type --extend=layout multiple times for multy extention layouts.} 
                          {--s|section=* : a section blade commands, you can type --section=setion multiple times for multy section blade file.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'create a new view in specific path under the view directory.';

    /**
     * The view path of MVC pattern.
     *
     * @var string
     */
    private $viewPath = '';

    /**
     * the file handle of open view file.
     *
     * @var string
     */
    private  $file;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->viewPath = base_path()."/resources/views/";
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $path = $this->argument('path');
        $options = $this->options('extends');
        $viewPath = $this->viewPath;
        $viewname = "";
        $tok = strtok($path,'/');
        while ($tok !== false) {
            
            if(strpos($tok,'.blade.php') != null) {
                $viewname = $tok;
                if(!file_exists($viewPath.$viewname)){
                    $this->file = fopen($viewPath.$viewname, 'w');
                }else {
                    $this->error("\n\n\tThe view ".$viewname." already exists.\n\n");
                    if($this->confirm('would you like to replace view '.$viewname.'?, all contents will be remove')){
                        $this->file = fopen($viewPath.$viewname, 'w');
                    }else { 
                        exit;   
                    }
                }
            }else {
                if(!is_dir($viewPath.$tok)) {
                    mkdir($viewPath.$tok);
                }
                $viewPath .= $tok.'/';
            }

            $tok = strtok("/");
        }

        $this->add_extends_blade_command($options);
        $this->add_section_blade_command($options);
  
        
        fclose($this->file);
        $this->info("\n\n\tView ".$viewname." successfuly created.\n\n");
    }

    private function add_extends_blade_command($options) {
        foreach($options['extends'] as $master){
            $masterPath = $this->changePath($master);
            
            if(file_exists($this->viewPath.$masterPath.".blade.php")){
                fprintf($this->file,"@extends('%s')\n",$master);  
            }else {
                fprintf($this->file,"\n<!-- was trying to add @extends blade command to extend this file from %s layout, but the layout at location %s.blade.php doesn't exsists. -->\n",$master,$masterPath);
            }
        }
    }

    private function add_section_blade_command($options) {
        $sections = Array();
        foreach($options['extends'] as $extend) {
            if(file_exists($this->viewPath.$this->changePath($extend).".blade.php")) {
                //$layoutFile = fopen($this->viewPath.$this->changePath($extend).".blade.php",'r');
                foreach($options['section'] as $section) {
                    if($this->fsearch($this->viewPath.$this->changePath($extend).".blade.php",'@yield(\''.$section.'\')')) {
                        fprintf($this->file,"@section('%s')\n\n<!-- The Content of the section ".$section." goes here. -->\n\n@endsection\n\n",$section);
                        $sections[$section] = true;
                    }else $sections[$section] = false;
                }
            }
        }

        foreach($sections as $key=>$value) {
            if(!$value) {
                fprintf($this->file,"\n<!-- was trying to add @section blade command -- @section('%s') -- but the section doesn't appears in extended layout files. -->\n",$key);
            }
        }
    }

    private function changePath($path) {
        $modifiedPath = '';
        if(strpos($path,'.') != null) {
            $modifiedPath = str_replace('.', '/' ,$path);
        }else $modifiedPath = $path;
        return $modifiedPath;
    }
    private function fsearch($filePath,$keyword) {
        $contents = file_get_contents($filePath);
        $searchfor = $keyword;
        $pattern = preg_quote($searchfor, '/');
        $pattern = "/^.*$pattern.*\$/m";
        if(preg_match_all($pattern, $contents, $matches)){
           return true;
        }else return false;
    }
}
