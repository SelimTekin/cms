<?php

class Product extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "product_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("product_model");
    }

    public function index(){
        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $items = $this->product_model->get_all(
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
            $insert = $this->product_model->add(
                array(
                    "title"       => $this->input->post("title"),
                    "description" => $this->input->post("description"),
                    "url"         => convertToSEO($this->input->post("title")),
                    "rank"        => 0, 
                    "isActive"    => 1,
                    "createdAt"   => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
                )
            );

            if($insert){
                redirect(base_url("product"));
            }
            else{
                redirect(base_url("product"));
            }
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
        $item = $this->product_model->get(
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
            $update = $this->product_model->update(
                array(
                    "id"          =>$id,
                ),
                array(
                    "title"       => $this->input->post("title"),
                    "description" => $this->input->post("description"),
                    "url"         => convertToSEO($this->input->post("title")),
                )
            );

            if($update){
                redirect(base_url("product"));
            }
            else{
                redirect(base_url("product"));
            }
        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // Tablodan verilerin getirilmesi
            $item = $this->product_model->get(
                array(
                    "id" => $id,
                )
            );

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "update";
            $viewData->form_error = true;
            $viewData->item = $item;

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function delete($id){
        $delete = $this->product_model->delete(
            array(
                "id"    => $id,
            )
        );

        // TODO ALert Sistemi Eklenecek...
        if($delete){
            redirect(base_url("product"));
        }
        else{
            redirect(base_url("product"));
        }
    }

    public function isActiveSetter($id){
        
        if($id){
            
            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->product_model->update( # update 2 tane parametre alır where,data
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
            $this->product_model->update(
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

    public function image_form($id){

        $viewData = new stdClass();

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $viewData->item = $this->product_model->get(
            array(
                "id"    => $id,
            )
        );

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

    }

    public function image_upload(){

        // Bunlar ayar( konfigürasyon(config) ) oluyor
        // $config["allowed_types"] = "*"; # bütün tipler veya
        $config["allowed_types"] = "jpg|jpeg|png"; # hangi türde dosyayı yükleyeceğimiz(yazarken aralarda boşluk bırakmadan yaz)
        $config["upload_path"] = "uploads/$this->viewFolder/"; # Dosyalar nereye yüklencek

        # upload sınıfını yüklerken nasıl yüklemek istediğimizi ya da ne şartlarda ya da nereye yükleyeceğizmizi belirttik $config ile
        $this->load->library("upload", $config);

        // upload sınıfındaki do_upload metodunu kullanarak bu işlemi(upload işlemini) gerçekleştirme
        $upload = $this->upload->do_upload("file"); # Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor

        if($upload){
            echo "islem basarili";
        }
        else{
            echo "islem basarisiz";
        }

    }
}