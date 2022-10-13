<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $viewFolder = "";

	public function __construct(){ # bu class çağırıldığında ilk çalıştırılacak olan metottur.
		parent::__construct(); #CI_Controller class'ı içindeki construct metodunu çalıştırmak için

		$this->viewFolder = "dashboard_v";
	}

	public function index()
	{
		$viewData = new stdClass();
		$viewData->viewFolder = $this->viewFolder;
		$viewData->subViewFolder = "list";
		$this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}
}
