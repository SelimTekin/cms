<?php

class Settings extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "settings_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("settings_model");

        // Bir controller içeerisindeki bir metod çağırıldığında ilk olarak construct metodu çağırılır.
		// O yüzden bütün metodların içerisinde yapmak yerine burada yaptık bu işlemi.
		if(!get_active_user()){ # !get_active_user() -> get_active_user() false döndürüyorsa demek.

			redirect("login");

		}
    }

    public function index(){
        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $item = $this->settings_model->get();

        if($item)
            $viewData->subViewFolder = "update";
        else
            $viewData->subViewFolder = "no_content";

        // View'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->item = $item;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)
    }

    public function new_form(){

        $viewData = new stdClass();

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "add";

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function save(){

        $this->load->library("form_validation");

        if($_FILES["logo"]["name"] == ""){ # herhangi bir resim seçip seçmediğini kontrol ediyoruz($_FILES'da logo isimli bir indis varsa ve bu indisin içerisindeki name alanı boşsa demek oluyor bu kısım)

            $alert = array(
                "title"  => "İşlem Başarısız",
                "text"   => "Lütfen bir görsel seçiniz",
                "type"   => "error"
            );

            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("settings/new_form"));

            die();

        }

        // Kurallar yazilir
        $this->form_validation->set_rules("company_name", "Şirket Adı", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)
        $this->form_validation->set_rules("phone_1", "Telefon 1", "required|trim");
        $this->form_validation->set_rules("email", "E-posta Adresi", "required|trim|valid_email");

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli bir <b>{field}</b> giriniz" 
            )
        );

        // Form validation calistirilir
        // TRUE - FALSE
        $validate = $this->form_validation->run();

        // Basarili ise
            // Kayit islemi baslar
        // Basarisiz ise
            // Hata ekranda gosterilir...

        if($validate){

            // Upload Süreci...

            // content dosyasındaki görsel inputunun name'i logo olduğu için logo yazdık. Yoksa dropzone'da file olarak gönderiliyordu.
            $file_name = convertToSEO($this->input->post("company_name")) . "." . pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION); # company_name'i direkt convertToSEO'ya verdik sonra uzantıyla birleştirdik. Yani her ikisini de convertToSEO içine almadık.

            # uzantı ve dosya ismini ayrı ayrı almak istersek aşağıda yazıyor
            // $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); # uzantıyı aldık
            // $file_name = convertToSEO(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)); # dosya ismini uzantısız aldık

            // Bunlar ayar( konfigürasyon(config) ) oluyor
            // $config["allowed_types"] = "*"; # bütün tipler veya
            $config["allowed_types"] = "jpg|jpeg|png"; # hangi türde dosyayı yükleyeceğimiz(yazarken aralarda boşluk bırakmadan yaz)
            $config["upload_path"] = "uploads/$this->viewFolder/"; # Dosyalar nereye yüklencek
            $config["file_name"] = $file_name;

            # upload sınıfını yüklerken nasıl yüklemek istediğimizi ya da ne şartlarda ya da nereye yükleyeceğizmizi belirttik $config ile
            $this->load->library("upload", $config);

            // upload sınıfındaki do_upload metodunu kullanarak bu işlemi(upload işlemini) gerçekleştirme
            $upload = $this->upload->do_upload("logo"); # Bizim inputumuzun name'i logo olduğu için logo yazdık.Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor.

            if($upload){

                # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
                $uploaded_file = $this->upload->data("file_name");

                $insert = $this->settings_model->add(
                    array(
                        "company_name" => $this->input->post("company_name"),
                        "phone_1"      => $this->input->post("phone_1"),
                        "phone_2"      => $this->input->post("phone_2"),
                        "fax_1"        => $this->input->post("fax_1"),
                        "fax_2"        => $this->input->post("fax_2"),
                        "address"      => $this->input->post("address"),
                        "about_us"     => $this->input->post("about_us"),
                        "mission"      => $this->input->post("mission"),
                        "vision"       => $this->input->post("vision"),
                        "email"        => $this->input->post("email"),
                        "facebook"     => $this->input->post("facebook"),
                        "twitter"      => $this->input->post("twitter"),
                        "instagram"    => $this->input->post("instagram"),
                        "linkedin"     => $this->input->post("linkedin"),
                        "logo"         => $uploaded_file,
                        "createdAt"    => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
                    )
                );

                if($insert){

                    $alert = array(
                        "title"  => "İşlem Başarılı",
                        "text"   => "Kayıt başarılı bir şekilde eklendi",
                        "type"   => "success"
                    );

                }
                else{

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Kayıt ekleme sırasında bir problem oluştu",
                        "type"   => "error"
                    );

                }

            }
            else{

                $alert = array(
                    "title"  => "İşlem Başarısız",
                    "text"   => "Görsel yüklenirken bir problem oluştu",
                    "type"   => "error"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url("settings/new_form"));

                die();

            }

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("settings"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "add";
            $viewData->form_error = true;

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_form($id){

        $viewData = new stdClass();

        // Tablodan Verilerin Getirilmesi
        $item = $this->settings_model->get(
            array(
                "id"    => $id,
            )
        );
        
        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "update";
        $viewData->item = $item;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);


    }

    public function update($id){

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("company_name", "Şirket Adı", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)
        $this->form_validation->set_rules("phone_1", "Telefon 1", "required|trim");
        $this->form_validation->set_rules("email", "E-posta Adresi", "required|trim|valid_email");

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli bir <b>{field}</b> giriniz"
            )
        );

        // Form validation calistirilir
        // TRUE - FALSE
        $validate = $this->form_validation->run();

        // Basarili ise
            // Kayit islemi baslar
        // Basarisiz ise
            // Hata ekranda gosterilir...

        if($validate){

            // Upload Süreci...

            if($_FILES["logo"]["name"] !== ""){

                // content dosyasındaki görsel inputunun name'i logo olduğu için logo yazdık. Yoksa dropzone'da file olarak gönderiliyordu.
                $file_name = convertToSEO($this->input->post("company_name")) . "." . pathinfo($_FILES["logo"]["name"], PATHINFO_EXTENSION); # company_name'i direkt convertToSEO'ya verdik sonra uzantıyla birleştirdik. Yani her ikisini de convertToSEO içine almadık.

                # uzantı ve dosya ismini ayrı ayrı almak istersek aşağıda yazıyor
                // $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); # uzantıyı aldık
                // $file_name = convertToSEO(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)); # dosya ismini uzantısız aldık

                // Bunlar ayar( konfigürasyon(config) ) oluyor
                // $config["allowed_types"] = "*"; # bütün tipler veya
                $config["allowed_types"] = "jpg|jpeg|png"; # hangi türde dosyayı yükleyeceğimiz(yazarken aralarda boşluk bırakmadan yaz)
                $config["upload_path"] = "uploads/$this->viewFolder/"; # Dosyalar nereye yüklencek
                $config["file_name"] = $file_name;

                # upload sınıfını yüklerken nasıl yüklemek istediğimizi ya da ne şartlarda ya da nereye yükleyeceğizmizi belirttik $config ile
                $this->load->library("upload", $config);

                // upload sınıfındaki do_upload metodunu kullanarak bu işlemi(upload işlemini) gerçekleştirme
                $upload = $this->upload->do_upload("logo"); # Bizim inputumuzun name'i logo olduğu için logo yazdık.Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor.

                if($upload){

                    # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
                    $uploaded_file = $this->upload->data("file_name");

                    $data = array(
                        "company_name" => $this->input->post("company_name"),
                        "phone_1"      => $this->input->post("phone_1"),
                        "phone_2"      => $this->input->post("phone_2"),
                        "fax_1"        => $this->input->post("fax_1"),
                        "fax_2"        => $this->input->post("fax_2"),
                        "address"      => $this->input->post("address"),
                        "about_us"     => $this->input->post("about_us"),
                        "mission"      => $this->input->post("mission"),
                        "vision"       => $this->input->post("vision"),
                        "email"        => $this->input->post("email"),
                        "facebook"     => $this->input->post("facebook"),
                        "twitter"      => $this->input->post("twitter"),
                        "instagram"    => $this->input->post("instagram"),
                        "linkedin"     => $this->input->post("linkedin"),
                        "logo"         => $uploaded_file,
                        "updatedAt"    => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
                    );

                }
                else{

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Görsel yüklenirken bir problem oluştu",
                        "type"   => "error"
                    );

                    $this->session->set_flashdata("alert", $alert);

                    redirect(base_url("settings/update_form/$id"));

                    die();

                }
            }
            else{
                # kullanıcı logo seçmediği zaman burası çalışır. logo alanını sildik dikkat ederseniz
                $data = array(
                    "company_name" => $this->input->post("company_name"),
                    "phone_1"      => $this->input->post("phone_1"),
                    "phone_2"      => $this->input->post("phone_2"),
                    "fax_1"        => $this->input->post("fax_1"),
                    "fax_2"        => $this->input->post("fax_2"),
                    "address"      => $this->input->post("address"),
                    "about_us"     => $this->input->post("about_us"),
                    "mission"      => $this->input->post("mission"),
                    "vision"       => $this->input->post("vision"),
                    "email"        => $this->input->post("email"),
                    "facebook"     => $this->input->post("facebook"),
                    "twitter"      => $this->input->post("twitter"),
                    "instagram"    => $this->input->post("instagram"),
                    "linkedin"     => $this->input->post("linkedin"),
                    "updatedAt"    => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
                );

            }

            $update = $this->settings_model->update(array("id" => $id), $data);

            if($update){

                $alert = array(
                    "title"  => "Güncelleme Başarılı",
                    "text"   => "Kayıt başarılı bir şekilde güncellendi",
                    "type"   => "success"
                );

            }
            else{

                $alert = array(
                    "title"  => "Güncelleme Başarısız",
                    "text"   => "Kayıt Güncelleme sırasında bir problem oluştu",
                    "type"   => "error"
                );

            }

            # Session Update İşlemi
            $settings = $this->settings_model->get();
            $this->session->set_userdata("settings", $settings);

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("settings"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "update";
            $viewData->form_error = true;

            // Tablodan verilerin getirilmesi
            $viewData->item = $this->settings_model->get(
                array(
                    "id" => $id,
                )
            );            

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

}