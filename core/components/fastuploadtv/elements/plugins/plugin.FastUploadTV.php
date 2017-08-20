<?php
$corePath = $modx->getOption('core_path',null,MODX_CORE_PATH).'components/fastuploadtv/';
$assetsUrl = $modx->getOption('assets_url',null,MODX_ASSETS_URL).'components/fastuploadtv/';

$modx->lexicon->load('fastuploadtv:default');

switch ($modx->event->name) {
    case 'OnTVInputRenderList':
        $modx->event->output($corePath.'elements/tv/input/');
        break;
    case 'OnTVInputPropertiesList':
        $modx->event->output($corePath.'elements/tv/input/options/');
        break;
    case 'OnDocFormPrerender':
        $js  = $modx->getOption('assets_url').'components/fastuploadtv/mgr/js/';
        $modx->regClientStartupScript($js.'widgets/modx.form.filefield.js');
        $modx->regClientStartupScript($js.'FastUploadTV.js');
        $modx->regClientStartupScript($js.'FastUploadTV.form.FastUploadTVField.js');
        break;
    case 'OnMODXInit':
        $mTypes = $modx->getOption('manipulatable_url_tv_output_types',null,'image,file').',fastuploadtv';
        $modx->setOption('manipulatable_url_tv_output_types', $mTypes);
        break;
    case 'OnFileManagerUpload':
        if ((bool)$modx->getOption('fastuploadtv.translit', null, false))
        {
            $fat = $modx->getOption('friendly_alias_translit');
            $friendly_alias_translit = (empty($fat) || $fat == 'none') ? false : true;
            
            foreach($files as $file)
            {
                if($file['error'] == 0)
                {
                    $pathInfo = pathinfo($file['name']);
                    $oldPath = $directory.$file['name'];
                    
                    $filename = modResource::filterPathSegment($modx, $pathInfo['filename']); // cleanAlias (translate)
                    if ($friendly_alias_translit)
                    {
                        $filename = preg_replace('/[^A-Za-z0-9_-]/', '', $filename); // restrict segment to alphanumeric characters only
                    }
                    $filename = preg_replace('/-{2,}/','-',$filename); // remove double symbol "-"
                    $filename = trim($filename, '-'); // remove first symbol "-"
                    
                    $newPath = $filename . '.' . strtolower($pathInfo['extension']);

                    if ($newPath !== $file['name']) {
                        $source->renameObject($oldPath, $newPath);
                    }
                }
            }
        }
        break;
}