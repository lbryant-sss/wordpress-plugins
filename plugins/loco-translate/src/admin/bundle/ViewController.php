<?php
/**
 * Bundle overview.
 * First tier bundle view showing resources across all projects
 */
class Loco_admin_bundle_ViewController extends Loco_admin_bundle_BaseController {
    
    
    /**
     * {@inheritdoc}
     */
    public function init(){
        parent::init();
        $bundle = $this->getBundle();
        $this->set('title', $bundle->getName() );
        $this->enqueueStyle('bundle');
    }


    /**
     * {@inheritdoc}
     */
    public function getHelpTabs(){
        return  [
            __('Overview','loco-translate') => $this->viewSnippet('tab-bundle-view'),
        ];
    }


    /**
     * Generate a link for a specific file resource within a project
     * @return string
     */
    private function getResourceLink( $page, Loco_package_Project $project, Loco_gettext_Metadata $meta ){
        return $this->getProjectLink( $page, $project, [
            'path' => $meta->getPath(false),
        ] );
    }


    /**
     * Generate a link for a project, but without being for a specific file
     * @return string
     */
    private function getProjectLink( $page, Loco_package_Project $project, array $args = [] ){
        $args['bundle'] = $this->get('bundle');
        $args['domain'] = $project->getId();
        $route = strtolower( $this->get('type') ).'-'.$page;
        return Loco_mvc_AdminRouter::generate( $route, $args );
    }


    /**
     * Initialize view parameters for a project
     * @return Loco_mvc_ViewParams
     */
    private function createProjectParams( Loco_package_Project $project ){
        $name = $project->getName();
        $domain = $project->getDomain()->getName();
        $slug = $project->getSlug();
        $p = new Loco_mvc_ViewParams(  [
            'id' => $project->getId(),
            'name' => $name,
            'slug' => $slug,
            'domain' => $domain,
            'short' => ! $slug || $project->isDomainDefault() ? $domain : $domain.'→'.$slug,
            'warnings' => [],
        ] );

        // POT template file
        $pot = null;
        $file = $project->getPot();
        if( $file && $file->exists() ){
            $pot = Loco_gettext_Metadata::load($file);
            $p['pot'] = new Loco_mvc_ViewParams( [
                // POT info
                'name' => $file->basename(),
                'time' => $file->modified(),
                // POT links
                'info' => $this->getResourceLink('file-info', $project, $pot ),
                'edit' => $this->getResourceLink('file-edit', $project, $pot ),
            ] );
        }
        
        // PO/MO files
        $po = $project->findLocaleFiles('po');
        $mo = $project->findLocaleFiles('mo');
        $p['po'] = $this->createProjectPairs( $project, $po, $mo );

        // also pull invalid files so everything is available to the UI
        $mo = $project->findNotLocaleFiles('mo');
        $po = $project->findNotLocaleFiles('po')->augment( $project->findNotLocaleFiles('pot') );
        $p['_po'] = $this->createProjectPairs( $project, $po, $mo );

        // offer msginit unless plugin settings disallows optional POT
        if( $pot || 2 > Loco_data_Settings::get()->pot_expected ){
            $p['nav'][] = new Loco_mvc_ViewParams( [ 
                'href' => $this->getProjectLink('msginit', $project ),
                'name' => __('New language','loco-translate'),
                'icon' => 'add',
            ] );
        }
        
        // Always offer PO file upload
        $p['nav'][] = new Loco_mvc_ViewParams( [
            'href' => $this->getProjectLink('upload', $project ),
            'name' => __('Upload PO','loco-translate'),
            'icon' => 'upload',
        ] );
        
        // prevent editing of POT when config prohibits
        if( $pot ){
            if( $project->isPotLocked() || 1 < Loco_data_Settings::get()->pot_protect ) {
                $p['nav'][] = new Loco_mvc_ViewParams( [
                    'href' => $this->getResourceLink('file-view', $project, $pot ),
                    'name' => __('View template','loco-translate'),
                    'icon' => 'file',
                ] );
            }
            // offer template editing if permitted
            else {
                $p['nav'][] = new Loco_mvc_ViewParams( [ 
                    'href' => $this->getResourceLink('file-edit', $project, $pot ),
                    'name' => __('Edit template','loco-translate'),
                    'icon' => 'pencil',
                ] );
            }
        }
        // else offer creation of new Template
        else {
            $p['nav'][] = new Loco_mvc_ViewParams( [ 
                'href' => $this->getProjectLink('xgettext', $project ),
                'name' => __('Create template','loco-translate'),
                'icon' => 'add',
            ] );
        }
        
        // foreach locale, establish if text domain is installed in system location, flag if not.
        $installed = [];
        foreach( $p['po'] as $pair ){
            $lc = $pair['lcode'];
            if( $pair['installed'] ){
                $installed[$lc] = true;
            }
            else if( ! array_key_exists($lc,$installed) ){
                $installed[$lc] = false;
            }
        }
        $p['installed'] = $installed;
        // warning only necessary for WP<6.6 due to `lang_dir_for_domain` fix
        if( ! function_exists('wp_get_l10n_php_file_data') && in_array(false,$installed,true) ){
            $p['warnings'][] = __('Custom translations may not work without System translations installed','loco-translate');
        }
        return $p;
    }


    /**
     * Collect PO/MO pairings, ignoring any PO that is in use as a template
     * @return array[]
     */
    private function createPairs( Loco_fs_FileList $po, Loco_fs_FileList $mo, ?Loco_fs_File $pot = null ):array {
        $pairs = [];
        /* @var $pofile Loco_fs_LocaleFile */
        foreach( $po as $pofile ){
            if( $pot && $pofile->equal($pot) ){
                continue;
            }
            $pair = [ $pofile, null ];
            $mofile = $pofile->cloneExtension('mo');
            if( $mofile->exists() ){
                $pair[1] = $mofile;
            }
            $pairs[] = $pair;
        }
        /* @var $mofile Loco_fs_LocaleFile */
        foreach( $mo as $mofile ){
            $pofile = $mofile->cloneExtension('po');
            if( $pot && $pofile->equal($pot) ){
                continue;
            }
            if( ! $pofile->exists() ){
                $pairs[] = [ null, $mofile ];
            }
        }
        return $pairs;
    }

    
    /**
     * Initialize view parameters for each row representing a localized resource pair
     * @return array collection of entries corresponding to available PO/MO pair.
     */
    private function createProjectPairs( Loco_package_Project $project, Loco_fs_LocaleFileList $po, Loco_fs_LocaleFileList $mo ):array {
        // populate official locale names for all found, or default to our own
        if( $locales = $po->getLocales() + $mo->getLocales() ){
            $api = new Loco_api_WordPressTranslations;
            foreach( $locales as $locale ){
                $locale->ensureName($api);
            }
        }
        // collate as unique [PO,MO] pairs ensuring canonical template excluded
        $pairs = $this->createPairs( $po, $mo, $project->getPot() );
        $rows = [];
        foreach( $pairs as $pair ){
            // favour PO file if it exists
            list( $pofile, $mofile ) = $pair;
            $file = $pofile or $file = $mofile;
            // establish locale, or assume invalid
            $locale = null;
            /* @var Loco_fs_LocaleFile $file */
            if( 'pot' !== $file->extension() ){
                $tag = $file->getSuffix();
                if( isset($locales[$tag]) ){
                    $locale = $locales[$tag];
                }
            }
            $rows[] = $this->createFileParams( $project, $file, $locale );
        }
        // Sort PO pairs in alphabetical order, with custom before system, before author
        usort( $rows, function( ArrayAccess $a, ArrayAccess $b ):int {
            return strcasecmp( $a['lname'], $b['lname'] );
        } );
        return $rows;
    }


    private function createFileParams( Loco_package_Project $project, Loco_fs_File $file, ?Loco_Locale $locale = null ):Loco_mvc_ViewParams {
        // Pull Gettext meta data from cache if possible
        $meta = Loco_gettext_Metadata::load($file);
        $dir = new Loco_fs_LocaleDirectory( $file->dirname() );
        $dType = $dir->getTypeId();
        // routing arguments
        $args =  [
            'path' => $meta->getPath(false), 
        ];
        // Return data required for PO table row
        return new Loco_mvc_ViewParams(  [
            // locale info
            'lcode' => $locale ? (string) $locale : '',
            'lname' => $locale ? $locale->getName() : '',
            'lattr' => $locale ? 'class="'.$locale->getIcon().'" lang="'.$locale->lang.'"' : '',
            // file info
            'meta' => $meta,
            'name' => $file->basename(),
            'time' => $file->modified(),
            'type' => strtoupper( $file->extension() ),
            'todo' => $meta->countIncomplete(),
            'total' => $meta->getTotal(),
            // author / system / custom / other
            'installed' => 'wplang' === $dType,
            'store' => $dir->getTypeLabel($dType),
            // links
            'view' => $this->getProjectLink('file-view', $project, $args ),
            'info' => $this->getProjectLink('file-info', $project, $args ),
            'edit' => $this->getProjectLink('file-edit', $project, $args ),
            'move' => $this->getProjectLink('file-move', $project, $args ),
            'delete' => $this->getProjectLink('file-delete', $project, $args ),
            'copy' => $this->getProjectLink('msginit', $project, $args ),
        ] );
    }

    
    /**
     * Prepare view parameters for all projects in a bundle
     * @return Loco_mvc_ViewParams[]
     */
    private function createBundleListing( Loco_package_Bundle $bundle ){
        $projects = [];
        /* @var $project Loco_package_Project */
        foreach( $bundle as $project ){
            $projects[] = $this->createProjectParams($project);
        }
        return $projects;
    }


    /**
     * {@inheritdoc}
     */
    public function render(){

        $this->prepareNavigation();
        
        $bundle = $this->getBundle();
        $this->set('name', $bundle->getName() );
        
        // bundle may not be fully configured
        $configured = $bundle->isConfigured();

        // Hello Dolly is an exception. don't show unless configured deliberately
        if( 'Hello Dolly' === $bundle->getName() && 'hello.php' === basename($bundle->getHandle()) ){
            if( ! $configured || 'meta' === $configured ){
                $this->set( 'redirect', Loco_mvc_AdminRouter::generate('core-view') );
                return $this->view('admin/bundle/alias');
            }
        }

        // Collect all configured projects
        $projects = $this->createBundleListing( $bundle );
        $unknown = [];
        
        // sniff additional unknown files if bundle is a theme or directory-based plugin that's been auto-detected
        if( 'file' === $configured || 'internal' === $configured ){
            // presumed complete
        }
        else if( $bundle->isTheme() || ( $bundle->isPlugin() && ! $bundle->isSingleFile() ) ){
            // TODO This needs abstracting into the Loco_package_Inverter class
            $prefixes = [];
            $po = new Loco_fs_LocaleFileList;
            $mo = new Loco_fs_LocaleFileList;
            $prefs = Loco_data_Preferences::get();
            foreach( Loco_package_Inverter::export($bundle) as $ext => $files ){
                $list = 'mo' === $ext ? $mo : $po;
                foreach( $files as $file ){
                    $file = new Loco_fs_LocaleFile($file);
                    $locale = $file->getLocale();
                    if( $prefs && ! $prefs->has_locale($locale) ){
                        continue;
                    }
                    $list->addLocalized( $file );
                    // Only look in system locations if locale is valid and domain/prefix available
                    if( $locale->isValid() ){
                        $domain = $file->getPrefix();
                        if( $domain ) {
                            $prefixes[$domain] = true;
                        }
                    }
                }
            }
            // pick up given files in system locations only
            foreach( $prefixes as $domain => $_bool ){
                $dummy = new Loco_package_Project( $bundle, new Loco_package_TextDomain($domain), '' );
                $bundle->addProject( $dummy ); // <- required to configure locations
                $dummy->excludeTargetPath( $bundle->getDirectoryPath() );
                $po->augment( $dummy->findLocaleFiles('po') );
                $mo->augment( $dummy->findLocaleFiles('mo') );
            }
            // a fake project is required to disable functions that require a configured project
            $dummy = new Loco_package_Project( $bundle, new Loco_package_TextDomain(''), '' );
            $unknown = $this->createProjectPairs( $dummy, $po, $mo );
        }
        
        $this->set('projects', $projects );
        $this->set('unknown', $unknown );

        return $this->view( 'admin/bundle/view' );
    }

   
}