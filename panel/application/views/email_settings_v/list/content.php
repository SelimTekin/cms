<div class="row">
    <div class="col-md-12">
		<h4 class="m-b-lg">
            E-posta Listesi
            <a href="<?php echo base_url("emailsettings/new_form"); # emailsettings controller'ı altındaki new_form metdou çağır ?>" class="btn btn-outline btn-primary btn-xs pull-right"><i class="fa fa-plus"></i> Yeni Ekle</a>
        </h4>
	</div>
    <div class="col-md-12">
		<div class="widget p-lg">

            <?php if(empty($items)){ ?>
                <div class="alert alert-info text-center">
                    <p>Burada herhangi bir kayıt bulunamadı. Eklemek için lütfen <a href="<?php echo base_url("emailsettings/new_form"); # emailsettings controller'ı altındaki new_form metdou çağır ?>">tıklayınız.</a></p>
                </div>
            <?php } else{ ?>
                <div class="table-responsive">
                    <table class="table table-hover table-striped table-bordered content-container">
                        <thead>
                            <th class="w50">#id</th>
                            <th>E-posta Başlık</th> <!-- Nereden geleceğini belirtecek -->
                            <th>Sunucu Adı</th>
                            <th>Protokol</th>
                            <th>Port</th>
                            <th>E-posta</th>
                            <th>Kimden</th>
                            <th>Kime</th>
                            <th>Durumu</th>
                            <th>İşlem</th>
                        </thead>
                        <tbody>
                            <?php foreach($items as $item){ ?>
                                <tr>
                                    <td class="w50 text-center">#<?php echo $item->id; ?></td>
                                    <td class="text-center"><?php echo $item->user_name; ?></td>
                                    <td class="text-center"><?php echo $item->host; ?></td>
                                    <td class="text-center"><?php echo $item->protocol; ?></td>
                                    <td class="text-center"><?php echo $item->port; ?></td>
                                    <td class="text-center"><?php echo $item->user; ?></td>
                                    <td class="text-center"><?php echo $item->from; ?></td>
                                    <td class="text-center"><?php echo $item->to; ?></td>
                                    <td class="text-center">
                                            <input
                                                data-url="<?php echo base_url("emailsettings/isActiveSetter/$item->id") ?>"
                                                class="isActive" 
                                                id="switch-2-2" 
                                                type="checkbox"
                                                data-switchery
                                                data-color="#10c469"
                                                <?php echo ($item->isActive) ? "checked" : ""; ?>
                                            />
                                    </td>
                                    <td class="text-center">
                                        <button 
                                            data-url="<?php echo base_url("emailsettings/delete/$item->id"); # id'yi parametre olarak verdik ?>"
                                            class="btn btn-sm btn-danger btn-outline remove-btn">
                                            <i class="fa fa-trash"></i> ___Sil___
                                        </button>
                                        <a href="<?php echo base_url("emailsettings/update_form/$item->id"); # id'yi parametre olarak verdik ?>" class="btn btn-sm btn-info btn-outline"><i class="fa fa-pencil-square-o"></i> Düzenle</a>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            <?php } ?>
		</div><!-- .widget -->
	    </div>
</div>