<?php

class Users extends CI_Controller{ # CI -> CodeIgniter (extend etmemizin sebebi bu class'ı CodeIgniter'ın bir controller olarak görmesi gerekiyor.)

    public $viewFolder = "";

    public function __construct(){
        parent::__construct(); # bu class her yüklendiğinde ortak olarak yüklenmesini istediğimiz bütün aksiyonları burada alırız.

        $this->viewFolder = "users_v";

        // construct'ın altında tanımlıyoruz yoksa load isimli metodu tanımaz
        $this->load->model("user_model");

        // Bir controller içeerisindeki bir metod çağırıldığında ilk olarak construct metodu çağırılır.
		// O yüzden bütün metodların içerisinde yapmak yerine burada yaptık bu işlemi.
		if(!get_active_user()){ # !get_active_user() -> get_active_user() false döndürüyorsa demek.

			redirect("login");

		}
    }

    public function index(){
        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $items = $this->user_model->get_all(
            array() # rank'e gerek yok (kullanıcıların sıralamasına gerek yok)
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
        $this->form_validation->set_rules("user_name", "Kullanıcı Adı", "required|trim|is_unique[users.user_name]"); # is_unique -> benzersiz mi diye kontrol eder. Mesela aynı kullanıcı adından bir tane daha var mı diye kontrol eder. users.user_name -> users tablosundaki user_name alanını temsil eder
        $this->form_validation->set_rules("full_name", "Ad Soyad", "required|trim");
        $this->form_validation->set_rules("email", "E-posta", "required|trim|valid_email|is_unique[users.email]"); # Girilen e-posta adresinin geçerli olup olmadığını kontrol eder
        $this->form_validation->set_rules("password", "Şifre", "required|trim|min_length[6]|max_length[8]"); # min ve max girilecek olan karakter sayısını belirliyoruz
        $this->form_validation->set_rules("re_password", "Şifre", "required|trim|min_length[6]|max_length[8]|matches[password]"); # tekrar girilen şifre password(ilki) ile eşleşiyor mu

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli e-posta adresi giriniz", 
                "is_unique"   => "<b>{field}</b> alanı daha önceden kullanılımş",
                "matches"     => "Şifreler birbiriyle uyuşmuyor",
                "min_length"  => "En az 6 karakterden oluşan şifre girmelisiniz",
                "max_length"  => "En fazla 8 karakterden oluşan şifre girmelisiniz",
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

            $insert = $this->user_model->add(
                array(
                    "user_name" => $this->input->post("user_name"),
                    "full_name" => $this->input->post("full_name"),
                    "email"     => $this->input->post("email"),
                    "password"  => md5($this->input->post("password")), # md5() fonksiyonu ile şifreledik
                    "isActive"  => 1,
                    "createdAt" => date("Y-m-d H:i:s") # yıl-ay-gun saat:dakika:saniye
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
            redirect(base_url("users"));

            die();
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
        $item = $this->user_model->get(
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

    public function update_password_form($id){

        $viewData = new stdClass();

        // Tablodan verilerin getirilmesi
        $item = $this->user_model->get(
            array(
                "id" => $id,
            )
        );

        // view'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "password";
        $viewData->item = $item;

        # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
    }

    public function update($id){

        $this->load->library("form_validation");

        $oldUser = $this->user_model->get(
            array(
                "id" => $id
            )
        );

        if($oldUser->user_name != $this->input->post("user_name")){
            $this->form_validation->set_rules("user_name", "Kullanıcı Adı", "required|trim|is_unique[users.user_name]"); # is_unique -> benzersiz mi diye kontrol eder. Mesela aynı kullanıcı adından bir tane daha var mı diye kontrol eder. users.user_name -> users tablosundaki user_name alanını temsil eder
        }

        if($oldUser->user_name != $this->input->post("email")){
            $this->form_validation->set_rules("email", "E-posta", "required|trim|valid_email[users.email]"); # Girilen e-posta adresinin geçerli olup olmadığını kontrol eder
        }

        // Kurallar yazilir
        $this->form_validation->set_rules("full_name", "Ad Soyad", "required|trim");
       
        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli e-posta adresi giriniz", 
                "is_unique"   => "<b>{field}</b> alanı daha önceden kullanılımş",
                "min_length"  => "En az 6 karakterden oluşan şifre girmelisiniz",
                "max_length"  => "En fazla 8 karakterden oluşan şifre girmelisiniz",
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

            $update = $this->user_model->update(
                array("id" => $id),

                array(
                    "user_name" => $this->input->post("user_name"),
                    "full_name" => $this->input->post("full_name"),
                    "email"     => $this->input->post("email"),
                )

            );

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
            redirect(base_url("users"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "update";
            $viewData->form_error = true;

            // Tablodan verilerin getirilmesi
            $viewData->item = $this->user_model->get(
                array(
                    "id" => $id,
                )
            );            

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function update_password($id){

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("password", "Şifre", "required|trim|min_length[6]|max_length[8]"); # min ve max girilecek olan karakter sayısını belirliyoruz
        $this->form_validation->set_rules("re_password", "Şifre", "required|trim|min_length[6]|max_length[8]|matches[password]"); # tekrar girilen şifre password(ilki) ile eşleşiyor mu
       
        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "matches"     => "Şifreler birbiriyle uyuşmuyor"
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

            $update = $this->user_model->update(
                array("id" => $id),

                array(
                    "password" => md5($this->input->post("password")),
                )

            );

            if($update){

                $alert = array(
                    "title"  => "Güncelleme Başarılı",
                    "text"   => "Şifreniz başarılı bir şekilde güncellendi",
                    "type"   => "success"
                );

            }
            else{

                $alert = array(
                    "title"  => "Güncelleme Başarısız",
                    "text"   => "Şifre güncelleme sırasında bir problem oluştu",
                    "type"   => "error"
                );

            }

            # İşlemin Sonucunu Session'a yazma işlemi...
            # session'a bir şey eklediğin zaman session'dan onu kaldırmadığın sürece session'da kalacaktır.
            # set_flashdata() ise bir kere set edilecek bir sonraki sayfa yenilenmesinde kendi kendine otoamtik şekilde kaldırılıyor codeigniter tarafından. 
            $this->session->set_flashdata("alert", $alert); # alert isimli bir indisim var ve bu alert isimli indisimin değeri $alert değişkeninden gelecek

            # if'in içinde değil de burada redirect etmemizin sebebi alert'e verileri gönderebilmek. if'in içinde oslaydı session satırına ulaşamadan sayfa redirect edilecekti.
            redirect(base_url("users"));

        }
        else{
            // Hata varsa yani input doldurulmamışsa mesela, sayfa yeniden yüklenecek
            $viewData = new stdClass();

            // view'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "password";
            $viewData->form_error = true;

            // Tablodan verilerin getirilmesi
            $viewData->item = $this->user_model->get(
                array(
                    "id" => $id,
                )
            );            

            # ikinci parametre olan $viewData'yı bu view'e gönderelim ki viewFolder ve subViewFolder'ı index sayfasında kullanabilelim
            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData);
        }
    }

    public function delete($id){
        $delete = $this->user_model->delete(
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
        redirect(base_url("users"));
    }

    public function isActiveSetter($id){

        if($id){

            $isActive = ($this->input->post("data") === "true") ? 1 : 0;

            $this->user_model->update( # update 2 tane parametre alır where,data
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
            $this->user_model->update(
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