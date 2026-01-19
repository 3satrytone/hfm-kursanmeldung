<?php

declare(strict_types=1);

namespace Hfm\Kursanmeldung\Controller;
use Hfm\Kursanmeldung\Domain\Repository\AnmeldestatusRepository;
use Hfm\Kursanmeldung\Domain\Repository\KursanmeldungRepository;
use Hfm\Kursanmeldung\Domain\Repository\ProfRepository;
use Psr\Http\Message\ResponseInterface;

/**
 * KurslisteController
 */
class KursListeController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {
	/**
     * hideIfClassMemberArr
     *
     * @var array hideIfClassMemberArr Keine Anzeige wenn UID aus anmeldestatus überienstimmt
     */
    protected $hideIfClassMemberArr = array(5,6);

    public function __construct(
        private readonly KursanmeldungRepository $kursanmeldungRepository,
        private readonly AnmeldestatusRepository $anmeldestatusRepository,
        private readonly ProfRepository $profRepository,
    )
    {
    }

    public function initializeAction(): void {
        if(isset($this->settings)){
            // if dbdata distributed over more pages
			if(isset($this->settings['dataPages'])){
				if(isset($this->KursanmeldungKursRepository))$this->KursanmeldungKursRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->hotelRepository))$this->hotelRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->profRepository))$this->profRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->gebuehrenRepository))$this->gebuehrenRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->orteRepository))$this->orteRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->ExportlistRepository))$this->ExportlistRepository->setPageIds($this->settings['dataPages']);
				if(isset($this->anmeldestatusRepository))$this->anmeldestatusRepository->setPageIds($this->settings['dataPages']);
			}
		}
		$disStat = $this->anmeldestatusRepository->findByReducetnart(1);
		
        if($disStat->count()>0){
			$this->hideIfClassMemberArr = array();
            foreach ($disStat as $key => $value) {
                array_push($this->hideIfClassMemberArr,$value->getUid());
            }
        }
		if(isset($this->settings['hideIfClassMemberArr'])) $this->hideIfClassMemberArr = $this->settings['hideIfClassMemberArr'];
			
	}

    /**
     * @return \Psr\Http\Message\ResponseInterface
     */
	public function listAction(): ResponseInterface {
		$kurse = array();
		if(isset($this->settings['kursids'])) $kurse = explode(',', $this->settings['kursids']);
	  	
	  	$kursanmeldungenGrouped = array();
        $kursanmeldungen = null;

        $showGrouped = array();
/*
        if(!empty($kurse)){
        	foreach($kurse as $kursid){
        		$kursanmeldungen = $this->kursanmeldungRepository->findByKursNotPassive($kursid);
                if(!empty($kursanmeldungen)){
        			foreach($kursanmeldungen as $kursanmeldung){
                        // nicht in der Liste Anzeigen wenn Status Abgesagt oder Abgemeldet ist
                        $showIfClassMember = true;
                        $status = $this->getAnmeldestatus($kursanmeldung);


                        if(in_array($status, $this->hideIfClassMemberArr) || $status === null){                      
                            $showIfClassMember = false;
                        }
                        // nur kurse mit aktiver Teilnahme = 0
		        		if($kursanmeldung->getTeilnahmeart() == 0){
                            // Kursdaten ermitteln
                            $kurs = $kursanmeldung->getKurs()->current();
    		        		if(!empty($kurs)){
    							$prof = $this->profRepository->findByUid($kurs->getProfessor());
    							if(!empty($prof)){
    								$kurs->setProf($prof->getName());
    							}
    			        		if($showIfClassMember){
                                    $kursanmeldungenGrouped[$kurs->getUid()]['kurs'][] = $kursanmeldung;
                                }
    			        		$kursanmeldungenGrouped[$kurs->getUid()]['showGrouped'] = 0;
                                $kursanmeldungenGrouped[$kurs->getUid()]['total']++;
    			        		if(isset($showGrouped[$kurs->getUid()])){
                                    $kursanmeldungenGrouped[$kurs->getUid()]['showGrouped'] = $showGrouped[$kurs->getUid()];
    			        		}
    			        	}
                            $tn = $kursanmeldung->getTn()->current();
                            if(!empty($tn)){
                                $tn->setLand($countries[$tn->getLand()]->$cntrName());
                            }
                        }
			        }
		        }
        	}
        }
        if($this->request->hasArgument('paginateSearch')){
        	$search = $this->request->getArgument('paginateSearch');
        	if(!empty($search)){
				
        		foreach ($search as $key => $value) {
					
        		    $kursanmeldungenSearch[$key] = $this->kursanmeldungRepository->findBySearchExtbase($key,$value);
					
					if($key == 0){
                        $kursanmeldungen = array();
        				$kursanmeldungen = $kursanmeldungenSearch[$key];
                        $kursanmeldungen['search'] = $value;

        			}else{
        				
                        if(!empty($kursanmeldungenSearch[$key])){
                            $kursanmeldungenGrouped[$key]['kurs'] = '';
                            foreach ($kursanmeldungenSearch[$key] as $skey => $svalue) {
                                // nicht in der Liste Anzeigen wenn Status Abgesagt oder Abgemeldet ist
                                $showIfClassMember = true;
                                $status = $this->getAnmeldestatus($svalue);
                                if(in_array($status, $this->hideIfClassMemberArr)){                            
                                    $showIfClassMember = false;
                                }
                                if($svalue->getTeilnahmeart() == 0){
                                    if($showIfClassMember)$kursanmeldungenGrouped[$key]['kurs'][] = $svalue;
                                }
                            }
                        }
                        $kursanmeldungenGrouped[$key]['search'] = $value;
                        $kursanmeldungenGrouped[$key]['showGrouped'] = 1;

        			}
        		}
        	}
			
        }

        $profSelStatus = $this->getStastusProfObj();
        if(!empty($profSelStatus)){
            $this->view->assign('profSelStatus', $profSelStatus);
        }
*/
        ksort($kursanmeldungenGrouped, SORT_NUMERIC );
        $this->view->assign('kursanmeldungen', $kursanmeldungen);
        $this->view->assign('kursanmeldungenGrouped', $kursanmeldungenGrouped);

        return $this->htmlResponse();
	}

    /**
     * action updatestatus
     *
     * @return void
     */
    public function updatestatusAction() {
        $kursid = $this->settings['kursids'];
        $tempData = $this->kursanmeldungRepository->findByKurs($kursid);
        $status = NULL;

        $updateStatus = array();
        if($this->request->hasArgument('anmeldestatuschanged')){
            $updateStatus = $this->request->getArgument('anmeldestatuschanged');
        }
        if(!empty($updateStatus)){
            // geänderte keys suchen
            foreach ($updateStatus as $key => $value) {
                // wenn kurse hinterlegt sind teilnehmer iterieren
                if(!empty($tempData)){
                    foreach ($tempData as $kursanmeldung) {
                        if($kursanmeldung->getUid() == $key){
                            $status = $this->anmeldestatusRepository->findByUid($value);
                            if($status != NULL){
                                $kursanmeldung->setProfstatus($status);
                                $this->kursanmeldungRepository->update($kursanmeldung);
                            }else{
								$kursanmeldung->setProfstatus(NULL);
                                $this->kursanmeldungRepository->update($kursanmeldung);
							}
                        }
                    }
                }
            }
        }

        $this->redirect('list');
    }

    /**
     * action list
     *
     * @return void
     */
    public function exportkursAction() {

		$kursid = 0;
		$type = 'PDF';
		$fileExt = 'pdf';
        $kursidAllowed = explode(',', $this->settings['kursids']);
		$typeAllowed = array('pdf', 'Excel5', 'Excel');
        
		if($this->request->hasArgument('kursid') &&  in_array($this->request->getArgument('kursid'), $kursidAllowed)){
            $kursid = $this->request->getArgument('kursid');
        }
        if($this->request->hasArgument('type') &&  in_array($this->request->getArgument('type'), $typeAllowed)){
            $type = $this->request->getArgument('type');
        }
		
		switch($type){
			case 'Excel5':
			case 'Excel':
				$fileExt = 'xls';
				break;
			case 'pdf':
				$type = 'PDF';
				$fileExt = 'pdf';
				break;
		}
		
		$tempData = $this->kursanmeldungRepository->findByKurs($kursid);
        
		$set = 0;
        $settings['data'] = $this->buildData($tempData,$set);
       
		$settings['filename'] = date('Y-m-d')."_kursanmeldung." . $fileExt;
        $settings['version'] = $type;
        $settings['size'] = 64;
		
        $this->Joexport->Joexport($settings, TRUE);

    }

    protected function getStastusProfObj(){
        $optionArr = array();
        $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,kurz','tx_jokursanmeldung_domain_model_anmeldestatus', 'deleted=0 AND hidden=0 AND kurz LIKE "prof\_%"');
        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
            $row['kurzTranslated'] = $this->joTranslate('tx_jokursanmeldung_domain_model_kursanmeldungliste.statusprof.'.$row['kurz']);
            array_push($optionArr,$row);
        }
        return $optionArr;
    }

    protected function buildData( $repData, $set=0 ){
        $data = array();
        $expArrFrm = $this->getExportArrFrames( $set );
        if(!empty($repData) && !empty($expArrFrm)){
            foreach($repData as $item){
                if(!empty($item) && get_class($item) == 'Justorange\JoKursanmeldung\Domain\Model\Kursanmeldung'){
                    $temp = array();
					$status = true;
                    foreach($expArrFrm['dataKey'] as $key=>$value){
                        // teilweise zweistufiges array
                        $keyArr = explode('.',$value);
                        if(!empty($keyArr)){
                            $method_name = 'get' . ucfirst($keyArr[0]);
                            $temp[$value] = $this->getValueFromMethodname( $method_name, $item );
                            if(isset($keyArr[1])){
                                $method_name = 'get' . ucfirst($keyArr[1]);
                                if(gettype($temp[$value]) == 'object' && count($temp[$value]) == 0){
									$temp[$value] = $this->getValueFromMethodname( $method_name, $temp[$value] );
                                }else{
                                    $tempArr = array();
                                    foreach($temp[$value] as $obj){
                                        array_push($tempArr, $this->getValueFromMethodname( $method_name, $obj ));
                                    }
                                    $temp[$value] = implode($tempArr,',');
                                }
                            }
							// bestimmte Werte übersetzen
							if(in_array($value,$this->translateKeys)){
								$temp[$value] = $this->joTranslate($value.'.'.$temp[$value]);
							}
							// bestimmte Werte aus DB
							if($value == 'tn.land'){
								 switch($GLOBALS['TSFE']->sys_language_isocode){
									case "de": $cntrName= 'getShortNameDe';break;
									case "en": $cntrName= 'getShortNameEn';break;
									default: $cntrName= 'getShortNameLocal';
								}
								$countries = array();
								$country = $this->countryRepository->findByUid($temp[$value]);
								$temp[$value] = $country->$cntrName();
							}
							if($value == 'anmeldestatus'){
								$retObj = NULL;
								if(is_object($temp[$value])){
									if($temp[$value]->current()!=NULL){
										$retObj = $temp[$value]->current();
										$temp[$value] = $retObj->getAnmeldestatus();
										if(in_array($retObj->getUid(), $this->hideIfClassMemberArr)){                            
											$status = false;
										}
									}
								}
							}
                        }
										
                    }
                    if($status) array_push($data, $temp);
                }
            }
            if(isset($expArrFrm['header'])){
                $tempArr = array();
                foreach($expArrFrm['header'] as $key=>$value){
                    $transl = $this->joTranslate($value['translate']);
                    $transl = ($transl == NULL)? $value['default'] : $transl;
                    array_push($tempArr, $transl); 
                }
                array_unshift ($data, $tempArr);
            }
        }
        return $data;
    }
    protected function getValueFromMethodname( $method_name, $obj ){
        $methValue = '';
        if(gettype($obj) == 'object' && method_exists ( $obj, $method_name )){
            $methValue = $obj->$method_name();
            if($methValue instanceof \DateTime){
                $methValue = $methValue->format('d.m.Y');
            }
        }
        return $methValue;
    }
    protected function getExportArrFrames( $set=0 ){
        $sets = array(
            0 => array(
                'header' => array(
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.datein'),
					array('type' => 0, 'default' => '', 'translate' => 'be_export.anmeldestatus'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.anrede'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.vorname'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.nachname'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.gebdate'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.email'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.ort'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldungteilnehmer.land'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.programm'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.orchesterstudio'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.comment'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.uploads'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.duosel'),
                    array('type' => 0, 'default' => '', 'translate' => 'tx_jokursanmeldung_domain_model_kursanmeldung.step3.duoname')
                ),
                'dataKey' => array(
                    'datein',
					'anmeldestatus',
                    'tn.anrede',
                    'tn.vorname',
                    'tn.nachname',
                    'tn.gebdate',
                    'tn.email',
                    'tn.ort',
                    'tn.land',
					'programm',
                    'orchesterstudio',
                    'comment',
                    'uploads.name',
                    'duosel',
                    'duoname'
                )
            )
        );
        $ret = false;
        if(isset($sets[$set])) $ret = $sets[$set];
        return $ret;
    }

    protected function getAnmeldestatus($kursanmeldung, $type='uid'){
        $retObj = NULL;
        $statusObj = $kursanmeldung->getAnmeldestatus();
        switch($type){
            case 'uid': if(is_object($statusObj)){
                            if($statusObj->current()!=NULL)$retObj = $statusObj->current()->getUid();
                        }
                        break;
            default:    if(is_object($statusObj)){
                            if($statusObj->current()!=NULL)$retObj = $statusObj->current();
                        }  
        }
        return $retObj;
    }

    protected function joTranslate() {
        $args = func_get_args();
        $key = array_shift($args);
        return \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, 'jo_kursanmeldung', $args);
    }

	/**
	* returns the kursname of given kursobj
	*
	* @return string
	*/
	protected function getKursname($kursActive, $onlyName=false){
		$kursname = '';
		if(!empty($kursActive) && $kursActive != NULL){
			$prof = $this->profRepository->findByUid($kursActive->getProfessor());
			// Name für Head
			if(!empty($prof) && $prof != NULL){
				$kursname = $prof->getName() . ' ' . $kursActive->getKurszeitstart()->format('d.m.Y') . ' - ' . $kursActive->getKurszeitend()->format('d.m.Y');
				if(!$onlyName){
					$kursname = '<span class="joHeadKurs"> | ' . $kursname . '</span>';
				}
			}
		}
		return $kursname;
	}

	private function getProfInfo($kursActive)
    {
        $profViewArr = array();

		if(!empty($kursActive) && $kursActive != NULL){
			//Profs
			$prof = $this->profRepository->findByUid($kursActive->getProfessor());
			if(!empty($prof)){
				$profViewArr['first_name'] = $prof->getName();
				$profViewArr['image'] = $prof->getImage();
				$profViewArr['path'] = '/uploads/pics/';
				$db = $this->getDb('typo3database');
		        if($_SERVER['SERVER_ADDR'] == '141.54.193.16' && $db){
		            $res = $db->exec_SELECTquery('*','tx_jobase_domain_model_personen', "uid= ".(int)$prof->getLink()." AND FIND_IN_SET(job, '0,2') AND sys_language_uid=0 ORDER BY last_name");
		            while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
		                $profViewArr = $row;
		                $profViewArr['path'] = 'https://www.hfm-weimar.de/uploads/tx_jobase/';
		            }
		        }
		    }
		}
		return $profViewArr;
    }

	private function getDb($dbname)
    {
        $db = false;
        if($_SERVER['SERVER_ADDR'] == '141.54.193.16' && $dbname == 'typo3database'){
            $db = new \TYPO3\CMS\Core\Database\DatabaseConnection();
            $db->setDatabaseHost('localhost');
            $db->setDatabaseName('typo3database');
            $db->setDatabaseUsername('typo3-database');
            $db->setDatabasePassword('71.sumulo');
            $db->connectDB();
        }
        return $db;
    }
}
