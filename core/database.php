<?php if(!defined('BASEPATH')) die("No direct script access");

namespace core;

/**
 * Database helper class. Database settings appear in /core/settings.ini. connect() returns a handle to a connection
 */ 
  class Database {

	//settings.ini and [Database] contains settings
	public $query = null;
	private $schema;
	private $username;
	private $password;
	private $host;
	private $dbh;
	
	public function __construct($handle='database'){
		$ini_array = parse_ini_file("databases.ini",true) or die("no database.ini file found");
		if(!isset($ini_array[$handle]))
			die("no database settings defined in settings.ini");
//		foreach ($ini_array[$handle] as $key->$value)
//		{
//				$this->dbh[$key]['handle'] =

	//	}
		$this->schema = $ini_array[$handle]['schema'];
		$this->username = $ini_array[$handle]['username'];
		$this->password = $ini_array[$handle]['password'];
		$this->host = $ini_array[$handle]['host'];
		define('TBL_PREFIX', $ini_array[$handle]['prefix']);
		
	}
	
	public function connect()
	{
		try {
		   $this->dbh = new PDO("mysql:host=".$this->host.";dbname=".$this->schema,
		   $this->username,$this->password);
		} catch (PDOException $e) {
		   die("Could not connect: " . $e->getMessage());
		}
		
		if (!$this->dbh) {
			die('Could not connect: '. mysql_error());
		}

		$check = $this->dbh->query("SHOW TABLES LIKE '" . TBL_PREFIX . "_interview_form'");
	
	    if ($check->rowCount() === 0) {
		   $this->create_tables();
		   $this->default_inserts();
		   
		}
		
		return $this->dbh;
	}
	
	public function default_inserts() {
	   $sql = "insert into `" . TBL_PREFIX . "_choice_type` (`id`, `type`) VALUES
				(0, 'Input'),
				(1, 'Radio'),
				(2, 'OnetoTenUnique'),
				(3, 'OnetoTen'),
				(4, 'Date'),
				(5, 'OnetoOneHundred'),
				(6, 'OnetoSixUnique'),
				(7, 'StartTime'),
				(8, 'EndTime'),
				(9, 'CheckBox'),
				(10, 'E_mail'),
				(11, 'Radio_Pair_Left'),
				(12, 'Radio_Pair_Right'),
				(13, 'Signature'),
				(14, 'TextArea'),
				(15, 'PhoneNumber'),
				(16, 'DropDown'),
				(17, 'OneToFiveUnique');";
		
		try {
			$value = $this->dbh->exec($sql);
			
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			die();
		}
	}
	
	public function create_tables() {
	
		$sql = "--
		-- Table structure for table `choice`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_choice` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `label` varchar(255) DEFAULT NULL,
		  `c_order` int(11) NOT NULL,
		  `question_id` int(11) NOT NULL,
		  `answer` varchar(255) DEFAULT NULL,
		  `type_id` int(11) NOT NULL,
		  `c_name` varchar(256) DEFAULT NULL,
		  `highlight` tinyint(1) NOT NULL,
		  PRIMARY KEY (`id`,`question_id`,`type_id`),
		  KEY `fk_choice_question1` (`question_id`),
		  KEY `fk_choice_choice_type1` (`type_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `choice_type`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_choice_type` (
		  `id` int(11) NOT NULL,
		  `type` varchar(45) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `form_assignment`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_form_assignment` (
		  `fetch_date` date NOT NULL,
		  `pmpid` int(11) NOT NULL,
		  `form_id` int(11) NOT NULL,
		  `signature` text NOT NULL,
		  PRIMARY KEY (`fetch_date`,`pmpid`,`form_id`),
		  KEY `fk_form_assignment_interview_form1` (`form_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;


		--
		-- Table structure for table `interview_form`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_interview_form` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(45) NOT NULL,
		  `numbered` tinyint(1) NOT NULL,
		  `timed` smallint(6) NOT NULL,
		  `begin_id` int(11) DEFAULT NULL,
		  `order` int(11) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `question`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_question` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `q_order` int(11) NOT NULL,
		  `question_text` varchar(1000) NOT NULL,
		  `section_id` int(11) NOT NULL,
		  `q_name` varchar(256) DEFAULT NULL,
		  `image` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`,`section_id`),
		  KEY `fk_question_section1` (`section_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `report`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_report` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `name` varchar(255) DEFAULT NULL,
		  `candidate_fields` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `report_choices`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_report_choices` (
		  `report_id` int(11) NOT NULL,
		  `choice_id` int(11) NOT NULL,
		  `choice_question_id` int(11) NOT NULL,
		  `choice_type_id` int(11) NOT NULL,
		  PRIMARY KEY (`report_id`,`choice_id`,`choice_question_id`,`choice_type_id`),
		  KEY `fk_report_has_question_report1` (`report_id`),
		  KEY `fk_report_has_question_choice1` (`choice_id`,`choice_question_id`,`choice_type_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `response`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_response` (
		  `choice_id` int(11) NOT NULL,
		  `question_id` int(11) NOT NULL,
		  `fetch_date` date NOT NULL,
		  `pmpid` int(11) NOT NULL,
		  `form_id` int(11) NOT NULL,
		  `user_entry` varchar(255) DEFAULT NULL,
		  PRIMARY KEY (`choice_id`,`question_id`,`fetch_date`,`pmpid`,`form_id`),
		  KEY `fk_response_choice1` (`choice_id`,`question_id`),
		  KEY `fk_response_form_assignment1` (`fetch_date`,`pmpid`,`form_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `section`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_section` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `title` varchar(128) DEFAULT NULL,
		  `s_order` int(11) NOT NULL,
		  `Interview_form_id` int(11) NOT NULL,
		  `hex_colour` varchar(7) DEFAULT NULL,
		  PRIMARY KEY (`id`,`Interview_form_id`),
		  KEY `fk_section_Interview_form1` (`Interview_form_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `validation`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_validation` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `choice_id` int(11) NOT NULL,
		  `type_id` int(11) NOT NULL,
		  PRIMARY KEY (`id`,`choice_id`,`type_id`),
		  KEY `fk_validation_choice1` (`choice_id`),
		  KEY `fk_validation_validation_type1` (`type_id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		-- --------------------------------------------------------

		--
		-- Table structure for table `validation_type`
		--

		CREATE TABLE IF NOT EXISTS `" . TBL_PREFIX . "_validation_type` (
		  `id` int(11) NOT NULL AUTO_INCREMENT,
		  `type` varchar(45) NOT NULL,
		  PRIMARY KEY (`id`)
		) ENGINE=InnoDB  DEFAULT CHARSET=latin1;

		--
		-- Constraints for dumped tables
		--

		--
		-- Constraints for table `choice`
		--
		ALTER TABLE `" . TBL_PREFIX . "_choice`
		  ADD CONSTRAINT `fk_choice_choice_type1` FOREIGN KEY (`type_id`) REFERENCES `" . TBL_PREFIX . "_choice_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  ADD CONSTRAINT `fk_choice_question1` FOREIGN KEY (`question_id`) REFERENCES `" . TBL_PREFIX . "_question` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

		--
		-- Constraints for table `form_assignment`
		--
		ALTER TABLE `" . TBL_PREFIX . "_form_assignment`
		  ADD CONSTRAINT `fk_form_assignment_interview_form1` FOREIGN KEY (`form_id`) REFERENCES `" . TBL_PREFIX . "_interview_form` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;


		--
		-- Constraints for table `question`
		--
		ALTER TABLE `" . TBL_PREFIX . "_question`
		  ADD CONSTRAINT `fk_question_section1` FOREIGN KEY (`section_id`) REFERENCES `" . TBL_PREFIX . "_section` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

		--
		-- Constraints for table `report_choices`
		--
		ALTER TABLE `" . TBL_PREFIX . "_report_choices`
		  ADD CONSTRAINT `fk_report_has_question_choice1` FOREIGN KEY (`choice_id`, `choice_question_id`, `choice_type_id`) REFERENCES `" . TBL_PREFIX . "_choice` (`id`, `question_id`, `type_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  ADD CONSTRAINT `fk_report_has_question_report1` FOREIGN KEY (`report_id`) REFERENCES `" . TBL_PREFIX . "_report` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

		--
		-- Constraints for table `response`
		--
		ALTER TABLE `" . TBL_PREFIX . "_response`
		  ADD CONSTRAINT `fk_response_choice1` FOREIGN KEY (`choice_id`, `question_id`) REFERENCES `" . TBL_PREFIX . "_choice` (`id`, `question_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  ADD CONSTRAINT `fk_response_form_assignment1` FOREIGN KEY (`fetch_date`, `pmpid`, `form_id`) REFERENCES `" . TBL_PREFIX . "_form_assignment` (`fetch_date`, `pmpid`, `form_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

		--
		-- Constraints for table `section`
		--
		ALTER TABLE `" . TBL_PREFIX . "_section`
		  ADD CONSTRAINT `fk_section_Interview_form1` FOREIGN KEY (`Interview_form_id`) REFERENCES `" . TBL_PREFIX . "_interview_form` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

		-- Constraints for table `validation`
		--
		ALTER TABLE `" . TBL_PREFIX . "_validation`
		  ADD CONSTRAINT `fk_validation_choice1` FOREIGN KEY (`choice_id`) REFERENCES `" . TBL_PREFIX . "_choice` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
		  ADD CONSTRAINT `fk_validation_validation_type1` FOREIGN KEY (`type_id`) REFERENCES `" . TBL_PREFIX . "_validation_type` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;";

		try {
			$value = $this->dbh->exec($sql);
		}
		catch(PDOException $e)
		{
			echo $e->getMessage();
			die();
		}
		
	}
}