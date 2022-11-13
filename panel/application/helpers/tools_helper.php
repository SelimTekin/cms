<?php

// bu fonksiyonu her sayfada kullanabiliriz. Bundan dolayı
function convertToSEO($text){

    $turkce = array("ç", "Ç", "ğ", "Ğ", "ü", "Ü", "ö", "Ö", "ı", "İ", "ş", "Ş", ".", ",", "!", "'", "\"", " ", "?", "*", "_", "|", "=", "(", ")", "[", "]", "{", "}");
    $convert = array("c", "c", "g", "g", "u", "u", "o", "o", "i", "i", "s", "s", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-", "-");

    return strtolower(str_replace($turkce, $convert, $text)); # test'te bulduğu turkce'deki indeksleri convert'te denk gelenlerle değiştirir.
}

function getFileName($id){

    /*
        Bu ÇALIŞMAZ çünkü helper içerisinde codeigniter çalışmadığından dolayı $this anahatar kelimesini kullanamayız. Çünkü Helper içi herhangi bir
        OOP içermiyor(Düz PHP fonksiyonu). Çalışması için $this'i elde etmemiz gerekiyor. Bunun için ise Codeigniter'ın bir instance'ını almamız gerekir.
        Bunu yapmak için $t = &gfet_instance(); yaparak $t değişkenini artık $this yerine kullanabiliriz.
    */
    // $fileName = $this->product_image_model->get(
    //     array(
    //         "id"    => $id
    //     )
    // );

    $t = &get_instance();

    $fileName = $t->product_image_model->get(
        array(
            "id"    => $id
        )
    );

    // Bu şekilde de olur ($myModel'i bu fonksiyonun bir paramtresi olarak yazıp Product.php dosyasında parametrenin değerini($this->product_image_model) verebiliriz)
    // $fileName = $myModel->get(
    //     array(
    //         "id"    => $id
    //     )
    // );

    return $fileName->img_url;
}

function switch_months($month){

    switch($month){

        case '1':
            $month = 'Ocak';
            break;
        case '2':
            $month = 'Şubat';
            break;
        case '3':
            $month = 'Mart';
            break;
        case '4':
            $month = 'Nisan';
            break;
        case '5':
            $month = 'Mayıs';
            break;
        case '6':
            $month = 'Haziran';
            break;
        case '7':
            $month = 'Temmuz';
            break;
        case '8':
            $month = 'Ağustos';
            break;
        case '9':
            $month = 'Eylül';
            break;
        case '10':
            $month = 'Ekim';
            break;
        case '11':
            $month = 'Kasım';
            break;
        case '12':
            $month = 'Aralık';
            break;

    }

    return $month;

}

function get_readable_date($date){

    list($date, $datetime) = explode(" ", date($date));
    list($date2_year, $date2_month, $date2_day) = explode("-", $date);

    $date2_month = switch_months($date2_month);

    return $date2_day . " " . $date2_month . " " . $date2_year;

    // Hata -> strftime fonksiyonu kullanımdan kalkmış
    // return strftime("%e %B %Y", strtotime($date)); # Buraya gelen datetime'ı direkt günü rakam olarak(%e), ay bilgisini kelime(metinsel) olarak(%B), yıl bilgisini de rakamsal olarak(%Y) veriyor.

}

function get_active_user(){

    $t = &get_instance();

    $user = $t->session->userdata("user"); # session'da user diye bi şey varsa

    if($user)
        return $user;
    else
        return false;

}

function send_email($toEmail = "", $subject = "", $message = ""){

    $t = &get_instance();

    $t->load->model("emailsettings_model");

    // $t->load->helper("string"); # panel/system/helpers/string_helper.php # Userop içerisinde kullandık (reset_password metodunda)

    // $temp_password = random_string(); # 8 karakterden oluşan random password üretir  # Userop içerisinde kullandık (reset_password metodunda)

    $email_settings = $t->emailsettings_model->get(

        array(
            "isActive" => 1
        )

    );


    $config = array(

        "protocol"  => $email_settings->protocol,
        "smtp_host" => $email_settings->host,
        "smtp_port" => $email_settings->port,
        "smtp_user" => $email_settings->user,
        "smtp_pass" => $email_settings->password,
        // "smtp_pass" => "royzktoitqwqisvw", # jvfwsireoxkzizdm, royzktoitqwqisvw pass -> password (kendi şifrenizi girmeniz gerekiyor yoksa hatayı ekranda gösterecektir)(hatayı yine de alacaksınız muhtemelen. Çözümü iki adımlı doğrulamayı açmak https://myaccount.google.com/security)(tek seferlik şifre alınıyor)
        "starttls"  => true, # güvenlikli bir email olması için kullanıyoruz. Yoksa spam'e düşebilir.
        "charset"   => "utf-8",
        "mailtype"  => "html", #gönderilen mail düz bir metinden mi oluşacak yoksa içerisine html tagı alacak mı resim ekleyecek miyiz.html olması mantıklı olur. Çünkü düz bir metin olsa da html içerisinde kabul edilecek
        "wordwrap"  => true, # kelime boşlukları olsun mu demek (true olsun demek oluyor). Mesela wordde satır sonuna gelince otomatik olarak alt satıra iner. İşte bu wordwrap oluyor.
        "newline"   => "\r\n" # macintosh ve windows işletim sistemlerinde bunu açan browser email'in içinde enter karakterini algılamak için...

    );

    $t->load->library("email", $config); # email kütüphanesini load ettik

    $t->email->from($email_settings->from, $email_settings->user_name); # nereden (CMS mail başlığı oluyor)
    $t->email->to($toEmail);           # nereye
    $t->email->subject($subject);      # konu
    $t->email->message($message); # emailin kendisi

    return $t->email->send(); # true ya da false dönecek zaten o yüzden if içinde sormamıza gerek yok. (Userop içerisinde soruyoruz if ile.(reset_password metodunda))

}