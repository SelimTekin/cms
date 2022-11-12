<?php

class Product extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "product_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("product_model");
        $this->load->model("product_image_model");

        // Bir controller içeerisindeki bir metod çağırıldığında ilk olarak construct metodu çağırılır.
		// O yüzden bütün metodların içerisinde yapmak yerine burada yaptık bu işlemi.
		if(!get_active_user()){ # !get_active_user() -> get_active_user() false döndürüyorsa demek.

			redirect("login");

		}
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

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("product"));

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
                $alert = array(
                    "title"  => "İşlem Başarılı",
                    "text"   => "Kayıt başarılı bir şekilde güncellendi",
                    "type"   => "success"
                );
            }
            else{
                $alert = array(
                    "title"  => "İşlem Başarısız",
                    "text"   => "Güncelleme sırasında bir problem oluştu",
                    "type"   => "success"
                );
            }
            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("product"));
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

        $delete = $this->product_image_model->delete(
            array(
                "product_id"    => $id
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
        redirect(base_url("product"));
    }

    public function imageDelete($id, $parent_id){

        $fileName = getFileName($id);

        $delete = $this->product_image_model->delete(
            array(
                "id"    => $id,
            )
        );

        // TODO ALert Sistemi Eklenecek...
        if($delete){

            unlink("uploads/{$this->viewFolder}/$fileName"); # bir dosyayı belirli bir yoldan silmek için bu komut kullanılır. (Mesela uploads klasörü altından fotoğraf kaldırmak için kullandık)

            redirect(base_url("product/image_form/$parent_id"));
        }
        else{
            redirect(base_url("product/image_form/$parent_id"));
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

    public function imageIsActiveSetter($id){

        if($id){

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->product_image_model->update( # update 2 tane parametre alır where,data
                array(
                    "id"    => $id,
                ),

                array(
                    "isActive"  => $isActive,
                )
                );

        }

    }

    public function isCoverSetter($id, $parent_id){

        if($id){

            $isCover = ($this->input->post("data") === "true") ? 1 : 0;

            // Kapak yapılmak istenen kayıt
            $this->product_image_model->update( # update 2 tane parametre alır where,data
                array(
                    "id"            => $id,
                    "product_id"    => $parent_id
                ),

                array(
                    "isCover"  => $isCover,
                )
            );

            // Kapak yapılmayan diğer kayıtlar
            $this->product_image_model->update( # update 2 tane parametre alır where,data
                array(
                    "id !="      => $id,
                    "product_id" => $parent_id
                ),

                array(
                    // Kapak fotoğrafı olarak seçilmeyenleri 0 yapıyoruz
                    // "isCover"       => ($isCover) ? 0 : 0, # her halükarda 0 yap (o yüzden direkt 0 yazmak daha mantıklı)
                    "isCover"   => 0,
                )
            );

            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "image";

            $viewData->item_images = $this->product_image_model->get_all( # view'e gönderebilmek için $viewData'nın bir property'si -attribute'ı- olarak tanımladık
                array(
                    "product_id"    => $parent_id
                ),"rank ASC"
                
            );

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/image_list_v", $viewData, true); # true olursa sayfada görünmez bu değişken içinde saklanır(echo $render_html ile ekranda görebiliriz). Buna render page deniyor
            echo $render_html;

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

    public function imageRankSetter(){

        $data = $this->input->post("data");

        parse_str($data, $order); # data'dan gelenleri order isimli değişkene aktar (data bir array ve &'leri patlatarak parse eder yani diziye aktarır)

        $items = $order["ord"];

        foreach($items as $rank => $id){ # rank->key, id->value ( Array( [0] => 6)) rank = 0, id = 6
            $this->product_image_model->update(
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

        $viewData->item_images = $this->product_image_model->get_all( # view'e gönderebilmek için $viewData'nın bir property'si -attribute'ı- olarak tanımladık
            array(
                "product_id"    => $id
            ),"rank ASC"
            
        );

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

    }

    public function image_upload($id){

        $file_name = convertToSEO(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

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
        $upload = $this->upload->do_upload("file"); # Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor

        if($upload){

            # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
            $uploaded_file = $this->upload->data("file_name");

            $this->product_image_model->add(
                array(
                    "img_url"       => $uploaded_file,
                    "rank"          => 0,
                    "isActive"      => 1,
                    "isCover"       => 0,
                    "createdAt"     => date("Y-m-d H:i:s"),
                    "product_id"    => $id
                )
            );

        }
        else{
            echo "islem basarisiz";
        }

    }

    public function refresh_image_list($id){

        $viewData = new stdClass();

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $viewData->item_images = $this->product_image_model->get_all( # view'e gönderebilmek için $viewData'nın bir property'si -attribute'ı- olarak tanımladık
            array(
                "product_id"    => $id
            )
        );

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/image_list_v", $viewData, true); # true olursa sayfada görünmez bu değişken içinde saklanır(echo $render_html ile ekranda görebiliriz). Buna render page deniyor
        echo $render_html;
    }
}