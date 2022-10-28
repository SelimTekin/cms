<?php

class Brands extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "brands_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("brand_model");
    }

    public function index(){
        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $items = $this->brand_model->get_all(
            array(), "rank ASC"
        );

        // View'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "list";
        $viewData->items = $items;

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

        if($_FILES["img_url"]["name"] == ""){ # herhangi bir resim seçip seçmediğini kontrol ediyoruz($_FILES'da img_url isimli bir indis varsa ve bu indisin içerisindeki name alanı boşsa demek oluyor bu kısım)

            $alert = array(
                "title"  => "İşlem Başarısız",
                "text"   => "Lütfen bir görsel seçiniz",
                "type"   => "error"
            );

            $this->session->set_flashdata("alert", $alert);

            redirect(base_url("brands/new_form"));

            die();

        }

        // Kurallar yazilir
        $this->form_validation->set_rules("title", "Başlık", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır" # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
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

            // content dosyasındaki görsel inputunun name'i img_url olduğu için img_url yazdık. Yoksa dropzone'da file olarak gönderiliyordu.
            $file_name = convertToSEO(pathinfo($_FILES["img_url"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["img_url"]["name"], PATHINFO_EXTENSION);

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
            $upload = $this->upload->do_upload("img_url"); # Bizim inputumuzun name'i img_url olduğu için img_url yazdık.Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor.

            if($upload){

                # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
                $uploaded_file = $this->upload->data("file_name");

                $insert = $this->brand_model->add(
                    array(
                        "title"       => $this->input->post("title"),
                        "img_url"   => $uploaded_file,
                        "rank"        => 0,
                        "isActive"    => 1,
                        "createdAt"   => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
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

                redirect(base_url("brands/new_form"));

                die();

            }

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("brands"));

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

        // Tablodan verilerin getirilmesi
        $item = $this->brand_model->get(
            array(
                "id" => $id,
            )
        );

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "update";
        $viewData->item = $item;

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id){

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("title", "Başlık", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

        $this->form_validation->set_message(
            array(
                "required" => "<b>{field}</b> alanı doldurulmalıdır" # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
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

            if($_FILES["img_url"]["name"] !== ""){

                // content dosyasındaki görsel inputunun name'i img_url olduğu için img_url yazdık. Yoksa dropzone'da file olarak gönderiliyordu.
                $file_name = convertToSEO(pathinfo($_FILES["img_url"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["img_url"]["name"], PATHINFO_EXTENSION);

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
                $upload = $this->upload->do_upload("img_url"); # Bizim inputumuzun name'i img_url olduğu için img_url yazdık.Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor.

                if($upload){

                    # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
                    $uploaded_file = $this->upload->data("file_name");

                    $data = array(
                            "title"       => $this->input->post("title"),
                            "img_url"   => $uploaded_file,
                        );

                }
                else{

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Görsel yüklenirken bir problem oluştu",
                        "type"   => "error"
                    );

                    $this->session->set_flashdata("alert", $alert);

                    redirect(base_url("brands/update_form/$id"));

                    die();

                }
            }
            else{

                $data = array(
                    "title"       => $this->input->post("title"),
                );

            }

            $update = $this->brand_model->update(array("id" => $id), $data);

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

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("brands"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "update";
            $viewData->form_error = true;

            // Tablodan verilerin getirilmesi
            $viewData->item = $this->brand_model->get(
                array(
                    "id" => $id,
                )
            );            

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function delete($id){
        $delete = $this->brand_model->delete(
            array(
                "id"    => $id,
            )
        );

        // TODO ALert Sistemi Eklenecek...
        if($delete){
            $alert = array(
                "title"  => "İşlem Başarılı",
                "text"   => "Kayıt başarılı bir şekilde silindi",
                "type"   => "success"
            );
        }
        else{
            $alert = array(
                "title"  => "İşlem Başarısız",
                "text"   => "Kayıt silme sırasında bir problem oluştu",
                "type"   => "success"
            );
        }
        $this->session->set_flashdata("alert", $alert);
        redirect(base_url("brands"));
    }

    public function isActiveSetter($id){

        if($id){

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->brand_model->update( # update 2 tane parametre alır where,data
                array(
                    "id"    => $id,
                ),

                array(
                    "isActive"  => $isActive,
                )
                );

        }

    }

    public function rankSetter(){

        $data = $this->input->post("data");

        parse_str($data, $order); # data'dan gelenleri order isimli değişkene aktar (data bir array ve &'leri patlatarak parse eder yani diziye aktarır)

        $items = $order["ord"];

        foreach($items as $rank => $id){ # rank->key, id->value ( Array( [0] => 6)) rank = 0, id = 6
            $this->brand_model->update(
                array(
                    "id"        => $id,
                    "rank !="   => $rank # sırası değiştirilen verinin altında kalanları değiştirmemek için. Yani konumu zaten değişmemişse bunu hiç değiştirme
                ),

                array(
                    "rank"      => $rank
                )
            );
        }

    }

}