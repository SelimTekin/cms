<?php

class Galleries extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "galleries_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("gallery_model");
        $this->load->model("image_model");
        $this->load->model("video_model");
        $this->load->model("file_model");

        // Bir controller içeerisindeki bir metod çağırıldığında ilk olarak construct metodu çağırılır.
		// O yüzden bütün metodların içerisinde yapmak yerine burada yaptık bu işlemi.
		if(!get_active_user()){ # !get_active_user() -> get_active_user() false döndürüyorsa demek.

			redirect("login");

		}
    }

    public function index(){
        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $items = $this->gallery_model->get_all(
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
        $this->form_validation->set_rules("title", "Galeri Adı", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

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

            $gallery_type = $this->input->post("gallery_type");
            $path         = "uploads/$this->viewFolder/";
            $folder_name  = "";

            if($gallery_type == "image"){

                $folder_name = convertToSEO($this->input->post("title"));
                $path        = "$path/images/$folder_name";

            }
            else if($gallery_type == "file"){

                $folder_name = convertToSEO($this->input->post("title"));
                $path        = "$path/files/$folder_name";

            }

            // $create_folder = mkdir($path, 0755); // klasör oluşturma fonksiyonu. Zorunlu olarak 3 tane parametre alır. path -> klasör adı, 0755 -> modu yani yazma izinleri
            
            if($gallery_type != "video"){

                if(!mkdir($path, 0755)){ # klasör oluşturulmazsa bu uyarıyı verir yoksa klasör oluşturur. ( Klasör oluşturma satırı nerede diye arama yani :) )

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Galeri üretilirken bir problem oluştu. (Yetki Hatası)",
                        "type"   => "error"
                    );

                    $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

                    redirect(base_url("galleries"));

                }

            }

            $insert = $this->gallery_model->add(
                array(
                    "title"        => $this->input->post("title"),
                    "gallery_type" => $this->input->post("gallery_type"),
                    "url"          => convertToSEO($this->input->post("title")),
                    "folder_name"  => $folder_name,
                    "rank"         => 0,
                    "isActive"     => 1,
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

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("galleries"));

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
        $item = $this->gallery_model->get(
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

    public function update($id, $gallery_type, $old_folder_name = ""){ # varsayılan olarak boşluk koyalım ki($old_folder_name parametresinden bahsediyorum) video olduğu zaman folder_name geleceği için burada bir hata ile karşılaşmayalım

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("title", "Galeri Adı", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

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

            $path         = "uploads/$this->viewFolder/";
            $folder_name  = "";

            if($gallery_type == "image"){

                $folder_name = convertToSEO($this->input->post("title"));
                $path        = "$path/images";

            }
            else if($gallery_type == "file"){

                $folder_name = convertToSEO($this->input->post("title"));
                $path        = "$path/files";

            }

            // $create_folder = mkdir($path, 0755); // klasör oluşturma fonksiyonu. Zorunlu olarak 3 tane parametre alır. path -> klasör adı, 0755 -> modu yani yazma izinleri
            
            if($gallery_type != "video"){

                if(!rename("$path/$old_folder_name", "$path/$folder_name")){ # 2 paramtre alır. eski klasör adı ve yeni klasör adı

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Galeri üretilirken bir problem oluştu. (Yetki Hatası)",
                        "type"   => "error"
                    );

                    $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

                    redirect(base_url("galleries"));
                    die();

                }

            }

            $update = $this->gallery_model->update(
                array(
                    "id"          =>$id,
                ),
                array(
                    "title"       => $this->input->post("title"),
                    "folder_name" => $folder_name,
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
            redirect(base_url("galleries"));
        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // Tablodan verilerin getirilmesi
            $item = $this->gallery_model->get(
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

        $gallery = $this->gallery_model->get(

            array(
                "id" => $id
            )

        );

        if($gallery){

            if($gallery->gallery_type != "video"){

                if($gallery->gallery_type == "image")
                    $path = "uploads/$this->viewFolder/images/$gallery->folder_name";
                else if($gallery->gallery_type == "file")
                    $path = "uploads/$this->viewFolder/files/$gallery->folder_name";

                $delete_folder = rmdir($path); # klasör silme. Tek parametre alır o da silinecek dosya.

                if(!$delete_folder){

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Kayıt silme sırasında bir problem oluştu",
                        "type"   => "error"
                    );

                    $this->session->set_flashdata("alert", $alert);
                    redirect(base_url("galleries"));

                    die();

                }

            }

            $delete = $this->gallery_model->delete(
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
                    "type"   => "error"
                );
            }

            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("galleries"));

        }

    }

    public function fileDelete($id, $parent_id, $gallery_type){

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        // $fileName = getFileName($id); # helpers klasörü altındaki tools_helper'da yazdığım fonksiyon (Bir şey değiştirmek için oraya git)

        $fileName = $this->$modelName->get(
            array(
                "id"    => $id
            )
        );

        $delete = $this->$modelName->delete(
            array(
                "id"    => $id,
            )
        );

        // TODO ALert Sistemi Eklenecek...
        if($delete){

            # Bir dosyayı belirli bir yoldan silmek için bu komut kullanılır. Aynı zamanda sunucudan da silmiş oluyoruz.(Mesela uploads klasörü altından fotoğraf kaldırmak için kullandık)
            unlink($fileName->url); 

            redirect(base_url("galleries/upload_form/$parent_id"));
        }
        else{
            redirect(base_url("galleries/upload_form/$parent_id"));
        }
    }

    public function isActiveSetter($id){

        if($id){

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->gallery_model->update( # update 2 tane parametre alır where,data
                array(
                    "id"    => $id,
                ),

                array(
                    "isActive"  => $isActive,
                )
                );

        }

    }

    public function fileIsActiveSetter($id, $gallery_type){

        if($id && $gallery_type){

            $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->$modelName->update( # update 2 tane parametre alır where,data
                array(
                    "id"    => $id,
                ),

                array(
                    "isActive"  => $isActive,
                )
                );

        }

    }

    public function fileRankSetter($gallery_type){

        $data = $this->input->post("data");

        parse_str($data, $order); # data'dan gelenleri order isimli değişkene aktar (data bir array ve &'leri patlatarak parse eder yani diziye aktarır)

        $items = $order["ord"];

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        foreach($items as $rank => $id){ # rank->key, id->value ( Array( [0] => 6)) rank = 0, id = 6
            $this->$modelName->update(
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

    public function rankSetter(){

        $data = $this->input->post("data");

        parse_str($data, $order); # data'dan gelenleri order isimli değişkene aktar (data bir array ve &'ları patlatarak parse eder yani diziye aktarır)

        $items = $order["ord"];

        foreach($items as $rank => $id){ # rank->key, id->value ( Array( [0] => 6)) rank = 0, id = 6
            $this->gallery_model->update(
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

    public function upload_form($id){

        $viewData = new stdClass();

        /** View'e gönderilecek Değişkenlerin Set Edilmesi.. */
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $item = $this->gallery_model->get(
            array(
                "id"    => $id
            )
        );

        $viewData->item = $item;

        if($item->gallery_type == "image"){

            $viewData->items = $this->image_model->get_all(
                array(
                    "gallery_id"    => $id
                ), "rank ASC"
            );

        }
        else if($item->gallery_type == "file"){

            $viewData->items = $this->file_model->get_all(
                array(
                    "gallery_id"    => $id
                ), "rank ASC"
            );

        }
        else{

            $viewData->items = $this->video_model->get_all(
                array(
                    "gallery_id"    => $id
                ), "rank ASC"
            );

        }

        $viewData->gallery_type = $item->gallery_type;
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function file_upload($gallery_id, $gallery_type, $folderName){

        $file_name = convertToSEO(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)) . "." . pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION);

        # uzantı ve dosya ismini ayrı ayrı almak istersek aşağıda yazıyor
        // $ext = pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION); # uzantıyı aldık
        // $file_name = convertToSEO(pathinfo($_FILES["file"]["name"], PATHINFO_FILENAME)); # dosya ismini uzantısız aldık

        // Bunlar ayar( konfigürasyon(config) ) oluyor
        // $config["allowed_types"] = "*"; # bütün tipler veya
        $config["allowed_types"] = "jpg|jpeg|png|pdf|doc|docx"; # hangi türde dosyayı yükleyeceğimiz(yazarken aralarda boşluk bırakmadan yaz)
        // $config["allowed_types"] = "jpg|jpeg|png|pdf|doc|docx"; // * -> bütün dosya türlerini temsil eder
        $config["upload_path"] = ($gallery_type == "image") ? "uploads/$this->viewFolder/images/$folderName" : "uploads/$this->viewFolder/files/$folderName"; # Dosyalar nereye yüklencek
        $config["file_name"] = $file_name;

        # upload sınıfını yüklerken nasıl yüklemek istediğimizi ya da ne şartlarda ya da nereye yükleyeceğizmizi belirttik $config ile
        $this->load->library("upload", $config);

        // upload sınıfındaki do_upload metodunu kullanarak bu işlemi(upload işlemini) gerçekleştirme
        $upload = $this->upload->do_upload("file"); # Neyi upload edeceğini dropzone'dan kaynaklı varsayılan olarak ismi(name) file olarak geliyor

        if($upload){

            # upload edilen dosya ile ilgili bilgilerin arasındaki ismini alabiliriz. data dediğimiz zaman array döndürür
            $uploaded_file = $this->upload->data("file_name");

            $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

            $this->$modelName->add(
                array(
                    "url"       => "{$config['upload_path']}/$uploaded_file",
                    "rank"          => 0,
                    "isActive"      => 1,
                    "createdAt"     => date("Y-m-d H:i:s"),
                    "gallery_id"    => $gallery_id
                )
            );

        }
        else{
            echo "islem basarisiz";
        }

    }

    public function refresh_file_list($gallery_id, $gallery_type){

        $viewData = new stdClass();

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "image";

        $modelName = ($gallery_type == "image") ? "image_model" : "file_model";

        $viewData->items = $this->$modelName->get_all( # view'e gönderebilmek için $viewData'nın bir property'si -attribute'ı- olarak tanımladık
            array(
                "gallery_id"    => $gallery_id
            )
        );

        $viewData->gallery_type = $gallery_type;

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $render_html = $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/render_elements/file_list_v", $viewData, true); # true olursa sayfada görünmez bu değişken içinde saklanır(echo $render_html ile ekranda görebiliriz). Buna render page deniyor
        echo $render_html;
    }
    
    public function gallery_video_list($id){
        $viewData = new stdClass();

        $gallery = $this->gallery_model->get(

            array("id" => $id)

        );

        // Tablodan verilerin getirilmesi
        $items = $this->video_model->get_all(
            array(
                "gallery_id" => $id
            ), "rank ASC"
        );

        // View'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/list";
        $viewData->items = $items;
        $viewData->gallery = $gallery;

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)
    }
    
    public function new_gallery_video_form($id){

        $viewData = new stdClass();

        $viewData->gallery_id = $id; # gallery_id değişkenini view'e gönderebiliyim ki video/add view'inde gallery_id'yi elde edebiliyim
        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/add";

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }
    
    public function gallery_video_save($id){

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("url", "Video URL", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

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

            $insert = $this->video_model->add(
                array(
                    "url"          => $this->input->post("url"),
                    "gallery_id"  => $id,
                    "rank"         => 0,
                    "isActive"     => 1,
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

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("galleries/gallery_video_list/$id"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "video/add";
            $viewData->form_error = true;
            $viewData->gallery_id = $id;

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_gallery_video_form($id){

        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $item = $this->video_model->get(
            array(
                "id" => $id,
            )
        );

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "video/update";
        $viewData->item = $item;

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);

    }

    public function gallery_video_update($id, $gallery_id){ # varsayılan olarak boşluk koyalım ki($old_folder_name parametresinden bahsediyorum) video olduğu zaman folder_name geleceği için burada bir hata ile karşılaşmayalım

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("url", "Video URL", "required|trim"); # input'un name'i, kural'ın(rule) ismi, kurallar (trim başındaki ve sonundaki boşlukları kontrol eder)

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

            $update = $this->video_model->update(
                array(
                    "id"          =>$id,
                ),
                array(
                    "url"       => $this->input->post("url"),
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
            redirect(base_url("galleries/gallery_video_list/$gallery_id"));
        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // Tablodan verilerin getirilmesi
            $item = $this->gallery_model->get(
                array(
                    "id" => $id,
                )
            );

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "video/update";
            $viewData->form_error = true;
            $viewData->item = $item;

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function rankGalleryVideoSetter(){

        $data = $this->input->post("data");

        parse_str($data, $order); # data'dan gelenleri order isimli değişkene aktar (data bir array ve &'ları patlatarak parse eder yani diziye aktarır)

        $items = $order["ord"];

        foreach($items as $rank => $id){ # rank->key, id->value ( Array( [0] => 6)) rank = 0, id = 6
            $this->video_model->update(
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
    
    public function galleryVideoIsActiveSetter($id){

        if($id){

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->video_model->update( # update 2 tane parametre alır where,data
                array(
                    "id"    => $id,
                ),

                array(
                    "isActive"  => $isActive,
                )
                );

        }

    }
    
    public function galleryVideoDelete($id, $gallery_id){

            $delete = $this->video_model->delete(
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
                    "type"   => "error"
                );
            }

            $this->session->set_flashdata("alert", $alert);
            redirect(base_url("galleries/gallery_video_list/$gallery_id"));

        }

}

