<?php

class Userop extends CI_COntroller{

    public $viewFolder = "";

    public function __construct(){

        parent::__construct();

        $this->viewFolder = "users_v";

        $this->load->model("user_model");

        # BU İŞLEM BURADA YAPILMAZ! ÇÜNKÜ CONTROLLER'DAN BİR METOD ÇAĞIRILDIĞI ZAMAN ÖNCE CONSTRUCT METOD ÇALIŞTIRILIR.
        # BURADA OLSAYDI SÜREKLİ ANASAYFAYA YÖNLENDİRECEKTİ ÇIKIŞ YAP' BASTIKÇA...
        // if(get_active_user()){
        //     redirect(base_url());
        // }

    }

    public function login(){ # Bunun için controller da oluşturulabilirdi

        # login işlemi olduysa login sayfasına geri döndürmeyi engellemek için...
        if(get_active_user()){
            redirect(base_url());
        }

        $viewData = new stdClass();

        // View'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "login";

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)
    
    }

    public function do_login(){

        # login işlemi olduysa login sayfasına geri döndürmeyi engellemek için...
        if(get_active_user()){
            redirect(base_url());
        }

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("user_email", "E-posta", "required|trim|valid_email"); # Girilen e-posta adresinin geçerli olup olmadığını kontrol eder
        $this->form_validation->set_rules("user_password", "Şifre", "required|trim|min_length[6]|max_length[8]"); # min ve max girilecek olan karakter sayısını belirliyoruz

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli e-posta adresi giriniz", 
                "min_length"  => "<b>{field}</b> en az 6 karakterden oluşmalıdır",
                "max_length"  => "<b>{field}</b> en fazla 8 karakterden oluşmalıdır",
            )
        );

        if($this->form_validation->run() == FALSE){

            # viewData oluşturmayıp redirect edersek hata alırız
            $viewData = new stdClass();

            // View'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "login";
            $viewData->form_error = true;

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)
    
        }
        else{

            $user = $this->user_model->get(

                array(
                    "email"    => $this->input->post("user_email"),
                    "password" => md5($this->input->post("user_password")),
                    "isActive" => 1
                )

            );

            if($user){


                $alert = array(
                    "title"  => "İşlem Başarılı",
                    "text"   => "$user->full_name hoşgeldiniz",
                    "type"   => "success"
                );

                $this->session->set_userdata("user", $user); # $user'ı user'a ata. flashdata demedik çünkü silinmesini istemiyoruz
                $this->session->set_flashdata("alert", $alert);

                redirect(base_url());

            }
            else{

                $alert = array(
                    "title"  => "İşlem Başarısız",
                    "text"   => "Lütfen giriş bilgilerinizi kontrol ediniz",
                    "type"   => "error"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url("login"));

            }

        }


    }

    public function logout(){

        $this->session->unset_userdata("user"); # user isimli indiste tuttuğumuz session'ı silme
        redirect(base_url("login"));

    }

    public function forget_password(){

        # login işlemi olduysa login sayfasına geri döndürmeyi engellemek için...
        if(get_active_user()){
            redirect(base_url());
        }

        $viewData = new stdClass();

        // View'e gönderilecek değişkenlerin set edilmesi
        $viewData->viewFolder = $this->viewFolder;
        $viewData->subViewFolder = "forget_password";

        $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)

    }

    public function reset_password(){

        $this->load->library("form_validation");

        // Kurallar yazilir
        $this->form_validation->set_rules("email", "E-posta", "required|trim|valid_email"); # Girilen e-posta adresinin geçerli olup olmadığını kontrol eder

        $this->form_validation->set_message(
            array(
                "required"    => "<b>{field}</b> alanı doldurulmalıdır", # field kuralın adına denk geliyor (örneğin yukarıdaki kuralın adı olan "Başlık")
                "valid_email" => "Lütfen geçerli e-posta adresi giriniz", 
            )
        );

        if($this->form_validation->run() == FALSE){

            # viewData oluşturmayıp redirect edersek hata alırız
            $viewData = new stdClass();

            // View'e gönderilecek değişkenlerin set edilmesi
            $viewData->viewFolder = $this->viewFolder;
            $viewData->subViewFolder = "forget_password";
            $viewData->form_error = true; # true yapıyoruz ki form hatası olduğu zaman görebilelim.

            $this->load->view("{$viewData->viewFolder}/{$viewData->subViewFolder}/index", $viewData); # viewData içindeki değişkenleri index dosyasında kullanabilmek için (product_v'yi viewFolder adında gönderecek)

        }
        else{

            $user = $this->user_model->get(

                array(
                    "isActive" => 1,
                    "email"    => $this->input->post("email")
                )

            );

            if($user){

                $this->load->helper("string");

                $temp_password = random_string();

                $send = send_email($user->email, "Şifremi Unuttum", "CMS'e geçici olarak <b>{$temp_password}</b> şifresiyle giriş yapabilirsiniz.");
        
                if($send){
                    echo "E-posta başarılı bir şekilde gönderilmiştir...";

                    $this->user_model->update(

                        array(
                            "id" => $user->id
                        ),
                        array(
                            "password" => md5($temp_password)
                        )

                    );

                    $alert = array(
                        "title"  => "İşlem Başarılı",
                        "text"   => "Şifreniz başarılı bir şekilde resetlendi. Lütfen E-postanızı kontrol ediniz!",
                        "type"   => "success"
                    );
    
                    $this->session->set_flashdata("alert", $alert);
    
                    redirect(base_url("login"));

                    die();

                }
                else{

                    // echo $this->email->print_debugger(); # email gönderilmemişse hatayı ekranda gösterme

                    $alert = array(
                        "title"  => "İşlem Başarısız",
                        "text"   => "Şifre gönderilirken bir problem oluştu!!",
                        "type"   => "error"
                    );
    
                    $this->session->set_flashdata("alert", $alert);
    
                    redirect(base_url("sifremi-unuttum"));

                    die();
                }

            }
            else{

                $alert = array(
                    "title"  => "İşlem Başarısız",
                    "text"   => "Böyle bir kullanıcı bulunamadı!!!",
                    "type"   => "error"
                );

                $this->session->set_flashdata("alert", $alert);

                redirect(base_url("sifremi-unuttum"));

            }

        }

        $this->input->post("email");

    }

}