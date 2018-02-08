<?php
namespace BackendBundle\DataMigration;



use BackendBundle\Entity\Agent;
use BackendBundle\Entity\AgentDistrict;
use BackendBundle\Entity\District;
use BackendBundle\Entity\Document;
use BackendBundle\Entity\Plan;
use BackendBundle\Entity\Subscription;
use BackendBundle\Entity\User;
use BackendBundle\Manager\DistrictManager;
use BackendBundle\Manager\PlanManager;
use BackendBundle\Manager\DocumentManager;
use BackendBundle\Manager\DocumentOptionManager;
use BackendBundle\Manager\AgentManager;
use BackendBundle\Manager\StateManager;
use BackendBundle\User\AccountTypeOptions;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class AgentLoader
 * @package Hero\BackendBundle\DataMigration
 */
class AgentLoader
{
// 0         ,1              ,2         ,3            ,4          ,5            ,6                  7,                8,     9,
// OfficeName,AccountingGroup,MemberPlan,LicenseRegion,LicenseType,LicenseNumber,DateLicenseExpires,DateFirstLicensed,MLSKey,MLSUserID,
// 10       ,11      ,12      ,13   ,14      ,15 ,16   ,17    ,18         ,19           ,20             ,21          ,22
// FirstName,LastName,FullName,Phone,PhoneExt,Fax,Email,Email2,HomeAddress,DateActivated,DateFirstJoined,TransferFrom,DateTerminated
    const COLUMN_DISTRICT           = 0;
    const COLUMN_STATE              = 1;
    const COLUMN_PLAN               = 2;
    const COLUMN_LICENSE_NUMBER     = 5;
    const COLUMN_LICENSE_EXPIRES    = 6;
    const COLUMN_FIRST_NAME         = 10;
    const COLUMN_LAST_NAME          = 11;
    const COLUMN_PHONE              = 13;
    const COLUMN_EMAIL              = 16;
    const COLUMN_EMAIL_TWO          = 17;
    const COLUMN_ADDRESS            = 18;
    const COLUMN_DATE_FIRST_JOINED  = 20;
    const COLUMN_TRANSFER_FROM      = 21;
    const COLUMN_DATE_TERMINATED    = 22;

    /**
     * @var AgentManager
     */
    private $agentManager;

    /**
     * @var StateManager
     */
    private $stateManager;

    /**
     * @var DistrictManager
     */
    private $districtManager;

    /**
     * @var PlanManager
     */
    private $planManager;

    /**
     * @var DocumentManager
     */
    private $documentManager;

    /**
     * @var DocumentOptionManager
     */
    private $documentOptionManager;

    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var OutputInterface
     */
    private $output;

    /** @var array  */
    private $states = [];

    /** @var array */
    private $plans = [];


    /**
     * AgentLoader constructor.
     * @param AgentManager       $agentManager
     * @param DocumentManagber	 $documentManager
     * @param DocumentOptionManager	 $documentOptionManager
     * @param StateManager       $stateManager
     * @param DistrictManager    $districtManager
     * @param PlanManager        $planManager
     * @param EntityManager      $em
     * @param ContainerInterface $container
     */
    public function __construct(
        AgentManager $agentManager,
	DocumentManager $documentManager,
	DocumentOptionManager $documentOptionManager,
        StateManager $stateManager,
        DistrictManager $districtManager,
        PlanManager $planManager,
        EntityManager $em,
        ContainerInterface $container
    ) {
        $this->agentManager = $agentManager;
	$this->documentManager = $documentManager;
	$this->documentOptionManager = $documentOptionManager;
        $this->stateManager = $stateManager;
        $this->districtManager = $districtManager;
        $this->planManager = $planManager;
        $this->em = $em;
        $this->container = $container;
    }

    /**
     * @param string $path
     */
    public function loadFile($path)
    {

	$link = mysql_connect('db.fathomly.com', 'scott', 'MarcoBrazil17') or die('Could not connect to database');
	mysql_select_db('subscription_dev') or die('Could not select database');

        $fileHandle = fopen($path, 'r');
        $this->loadData();

        $rows = count(file($path));
        //$progress = new ProgressBar($this->output, $rows);
        //$progress->start();

        $columns = fgetcsv($fileHandle);
        while ($data = fgetcsv($fileHandle)) {
		if (($data[self::COLUMN_STATE] != 'California') and ($data[self::COLUMN_DATE_TERMINATED] == '')) {

			// Make sure this isn't a dupe email...if it is, this is an agent in multiple markets, so treat them differently...
			$email = $data[self::COLUMN_EMAIL];
			$email2 = $data[self::COLUMN_EMAIL_TWO];
			$userRes = mysql_query("SELECT id FROM user WHERE email = '$email'");
			$badPE = (($agentRes = mysql_query("SELECT id FROM agent WHERE personal_email = '$email2'")) and (mysql_num_rows($agentRes) > 0));
			if (($userRes) and ($user = mysql_fetch_assoc($userRes))) {
				echo "Agent exists :: ".$data[self::COLUMN_LAST_NAME].", ".$data[self::COLUMN_FIRST_NAME];
				$userID = $user['id'];
				$stateName = strtolower(trim($data[self::COLUMN_STATE]));
				$districtName = strtolower(trim($data[self::COLUMN_DISTRICT]));
				if (($stateRes = mysql_query("SELECT id FROM state WHERE name = '$stateName'")) and ($state = mysql_fetch_assoc($stateRes))) {
					$stateID = $state['id'];
					if (($districtRes = mysql_query("SELECT id FROM district WHERE name = '$districtName' and state_id = $stateID")) and
						($district = mysql_fetch_assoc($districtRes))) {
						$districtID = $district['id'];
					}
					else {
						mysql_query("INSERT INTO district SET state_id = $stateID, name = '$districtName'");
						$districtID = mysql_insert_id();
					}
					$adRes = mysql_query("SELECT id FROM agent_district WHERE agent_id = $userID AND district_id = $districtID");
					if (mysql_num_rows($adRes) == 0) {
						echo ", market $districtName added";
						mysql_query("INSERT INTO agent_district SET agent_id = $userID, district_id = $districtID");
					}
					echo "\n";
				}
				else {
					echo "Bad state ($stateName)\n";
				}
			}
			else {
				if ($badPE) {
					$data[self::COLUMN_EMAIL_TWO] .= '_dupe';
				}
				$agent = $this->buildAgent($data);
				$this->agentManager->save($agent, true);
				$this->loadDocuments($agent,$data);

				/* clear unit of work to speed up */
				$this->em->getUnitOfWork()->clear();

				//$progress->advance();
			}
		}
	}

        //$progress->finish();
        fclose($fileHandle);

    }

    /**
     * @param array $data
     * @return Agent
     */
    public function buildAgent(array $data)
    {

        $agent = new Agent();

        $agent->setEmail($data[self::COLUMN_EMAIL]);
        if ($data[self::COLUMN_EMAIL_TWO]) {
            $agent->setPersonalEmail($data[self::COLUMN_EMAIL_TWO]);
        }

        $agent->setFirstName($data[self::COLUMN_FIRST_NAME]);
        $agent->setLastName($data[self::COLUMN_LAST_NAME]);
        $agent->setAddress($data[self::COLUMN_ADDRESS]);
        $agent->setPhoneNumber($data[self::COLUMN_PHONE]);
        $agent->setJoinedDate(new \DateTime($data[self::COLUMN_DATE_FIRST_JOINED]));


        /* find plan by name and create subscription */
        $planName = strtolower(trim($data[self::COLUMN_PLAN]));
        if ($planName && array_key_exists($planName, $this->plans)) {
            $planId = $this->plans[$planName];
            $subscription = new Subscription();
            $subscription->setPlan($this->em->getReference('BackendBundle:Plan', $planId));

            $subscription->setStatus(Subscription::STATUS_ACTIVE);
            $subscription->setDueDate(new \DateTime('first day of next month'));
            $subscription->setUser($agent);
            $agent->setSubscription($subscription);
        }

        /* associate Agent with give District */
        $stateName = strtolower(trim($data[self::COLUMN_STATE]));
        $districtName = strtolower(trim($data[self::COLUMN_DISTRICT]));
        if (array_key_exists($stateName, $this->states)) {
            $stateId = $this->states[$stateName]['id'];

            /* if district doesn't exist create it */
            if (!array_key_exists($districtName, $this->states[$stateName]['districts'])) {
                $district = new District();
                $district->setName($data[self::COLUMN_DISTRICT]);
                $district->setState($this->em->getReference('BackendBundle:State', $stateId));
                $this->districtManager->save($district);

                $this->states[$stateName]['districts'][$districtName] = $district->getId();
            }

            $districtId = $this->states[$stateName]['districts'][$districtName];
            $agentDistrict = new AgentDistrict();
            $agentDistrict->setAgent($agent);
            $agentDistrict->setDistrict($this->em->getReference('BackendBundle:District', $districtId));
            $agent->addAgentDistrict($agentDistrict);
        }

        /** TODO import these fields too */
        // self::COLUMN_LICENSE_NUMBER
        // self::COLUMN_TRANSFER_FROM

        $agent->setStatus(User::STATUS_ACTIVE);
        $agent->setRoles([AccountTypeOptions::AGENT]);
        $agent->setPlainPassword(md5('Fathom'.$data[self::COLUMN_FIRST_NAME].$data[self::COLUMN_LAST_NAME]));

	return $agent;

    }

    /**
     * @param array $data
     */
    public function loadDocuments($agent, array $data)
    {
	
	/* ...figure out what directory the agent's docs might be in... */
	$state		= $data[self::COLUMN_STATE];
	$district	= $data[self::COLUMN_DISTRICT];
	$rootPath	= "/storage/fathomdocs/$state";
	$agentLn	= $data[self::COLUMN_LAST_NAME];
	$agentFn	= $data[self::COLUMN_FIRST_NAME];
	$agentName	= "$agentLn, $agentFn";
	$patternRA	= array(
				"*/*$agentName*",
				"*/*/*$agentName*",
				"*/*$agentLn*",
				"*/*/*$agentLn*",
				"*$agentName*",
				"*$agentLn*"
			);
	$agentDir	= 'unknown';
	foreach ($patternRA as $pattern) {
        	foreach(glob("$rootPath/$pattern", GLOB_ONLYDIR) as $dir) {
                	$agentDir = $dir;
			break;
        	}
	}
	echo "$agentName/$state/$district ($agentDir dir)\n";
	foreach(glob("$agentDir/*.{*}",GLOB_BRACE) as $file) {
		$osfile = basename($file);
		$tmp = explode('/',$this->docType($osfile));
		$dt = $tmp[0];
		$description = $tmp[1];
		// dt = 0 means skip the file...
		if ($dt != '0') {
			$do = $this->documentOptionManager->find($dt);
			//echo "   $osfile, type = $dt, descript = $description\n";
			$document = New Document();
			$document->setAgent($agent);
			$document->setDocumentOption($do);
			$document->setStatus(1);
			$document->setDocumentName(str_replace('/storage/fathomdocs','',$file));
			$document->setUploadedDate(new \DateTime(date('Y-m-d G:i:s')));
			if ($dt == "1") {
				$document->setExpirationDate(new \DateTime(date('Y-m-d',strtotime($data[self::COLUMN_LICENSE_EXPIRES]))));
			}
			$document->setDescription($description);
			$this->documentManager->save($document, true);
		}
	}
    }

    /**
     * @param OutputInterface $output
     */
    public function setOutput(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * Load Plans, States, Districts
     */
    private function loadData()
    {
        /* Load all plans */
        $plans = $this->planManager->getFindAll();
        foreach ($plans as $plan) {
            $planName = $plan->getName();
            $planName = strtolower(trim($planName));

            $this->plans[$planName] = $plan->getId();
        }

        /* Load all States with districts */
        $this->states = $this->stateManager->getAllStatesForAgentImporter();
    }

    /**
     * @param string $file
    */
    public function skipFile ($file) {
        $skip = false;
        $needles = array("agents gone","former agents","market specific docs","company docs","z-old","zAgents","desktop");
        foreach ($needles as $needle) {
           if (strstr(strtolower($file),$needle)) {
              $skip = true;
              break;
           }
        }
    return $skip;
}

    /**
     * @param string $filenae
     */

    public function docType ($fn) {
	$tfn = str_replace(array("_","-"),"",strtolower($fn));
	if ($this->skipFile($fn)) return "0/";
	elseif ((strstr($tfn,"agent agreement")) or (strstr($tfn,"independent contractor"))) return "3/";
	elseif (strstr($tfn,"w9")) return "6/";
	elseif ((strstr($tfn,"schedule a")) or (strstr($tfn,"schedulea"))) return "4/";
	elseif ((strstr($tfn,"license")) or (strstr($fn,"REL")) or (strstr($fn,"TREC"))) return "1/";
	elseif (strstr($tfn,"mentor")) return "14/Mentor Agreement";
	elseif (strstr($tfn,"mentee")) return "14/Mentee Agreement";
	elseif ((strstr($tfn,"insur")) or (strstr($tfn,"ins-card"))) return "2/";
	elseif (strstr($tfn,"policy")) return "5/";
	elseif (strstr($tfn,"receipt")) return "14/Receipt";
	elseif ((strstr($tfn,"credit card")) or (strstr($tfn,"creditcard")) or (strstr($tfn,"cc auth")) or (strstr($tfn,"ccauth")) or
		(strstr($tfn,"cc info")) or (strstr($tfn,"ccinfo")) or (strstr($tfn,"mls auth"))) return "15/";
	elseif ((strstr($tfn,"hometown heroes")) or (strstr($tfn,"hth"))) return "16/";
	elseif (strstr($tfn,"business entity")) return "13/";
	elseif (strstr($tfn,"dpor")) return "14/DPOR";
	elseif (strstr($tfn,"direct deposit")) return "17/";
	elseif ((strstr($tfn,"of intent")) or (strstr($tfn,"ofintent"))) return "18/";
	elseif ((strstr($tfn,"pocket card")) or (strstr($tfn,"pocketcard"))) return "1/";
	elseif (strstr($tfn,"leads")) return "14/Leads";
	elseif (strstr($tfn,"change")) return "19/";
	elseif ((strstr($tfn,"board document")) or (strstr($tfn,"boarddocument")) or (strstr($tfn,"new agent")) or (strstr($tfn,"newagent")) or
		(strstr($tfn,"onboard"))) return "7/";
	else return "14/Unknown";
     }

}
