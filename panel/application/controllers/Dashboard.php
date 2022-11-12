<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends CI_Controller {

	public $viewFolder = "";
	// public $user;

	public function __construct(){ # bu class çağırıldığında ilk çalıştırılacak olan metottur.
		parent::__construct(); #CI_Controller class'ı içindeki construct metodunu çalıştırmak için

		$this->viewFolder = "dashboard_v";
		// $this->user = get_active_user(); # Aslında buna gerek yok. Ama böyle yaparsak bütün metodlar içerisinde kullanabiliriz.

		// Bir controller içeerisindeki bir metod çağırıldığında ilk olarak construct metodu çağırılır.
		// O yüzden bütün metodların içerisinde yapmak yerine burada yaptık bu işlemi.
		if(!get_active_user()){ # !get_active_user() -> get_active_user() false döndürüyorsa demek.

			redirect("login");

		}
	}

	public function index()
	{
		$viewData = new stdClass();
		$viewData->viewFolder = $this->viewFolder;
		$viewData->subViewFolder = "list";
		$this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
	}
}
