<?php
/*
	I.T. Web Experts, Rental Store v2
	http://www.itwebexperts.com

	Copyright (c) 2009 I.T. Web Experts

	This script and it's source is not redistributable
*/

    class InfoBoxPPRSearchFilter extends InfoBoxAbstract {

        public function __construct(){
            global $App;
            $this->init('pPRSearchFilter');
        }

        public function show(){
            global $appExtension;

            //return '';
            $htmlText = '<div class="pprfilter">';
            $boxWidgetProperties = $this->getWidgetProperties();
            if(isset($boxWidgetProperties->filters)){
                foreach($boxWidgetProperties->filters as $filtersData){
                    $htmlText .= '<li><a href="'.itw_app_link('&pprfp_start=' . $filtersData->start . '&pprfp_stop=' . $filtersData->stop, 'products', 'all').'">' . $filtersData->start . ' - ' . $filtersData->stop . '</a></li>';
                }

            }
            $htmlText .= '</div>';

            $this->setBoxContent($htmlText);
            return $this->draw();
        }

        public function buildStylesheet(){
            $styles = '.pprfilter {' .
                      'list-style:none;' .
                      'display:block;' .
                      'padding:0;' .
                      'margin:0;' .
                      '}' .
                      '.pprfilter li{' .
                      'vertical-align:middle;' .
                      '}';
            return $styles;
        }
    }
?>