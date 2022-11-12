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
