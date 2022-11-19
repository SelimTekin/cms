<div class="row">
    <div class="col-md-12">
		<h4 class="m-b-lg">
            Site Ayarları
            <a href="<?php echo base_url("settings/new_form"); # settings controller'ı altındaki new_form metdou çağır ?>" class="btn btn-outline btn-primary btn-xs pull-right"><i class="fa fa-plus"></i> Yeni Ekle</a>
        </h4>
	</div>
    <div class="col-md-12">
		<div class="widget p-lg">

            <?php if(empty($items)){ ?>
                <div class="alert alert-info text-center">
                    <p>Burada herhangi bir kayıt bulunamadı. Eklemek için lütfen <a href="<?php echo base_url("settings/new_form"); # settings controller'ı altındaki new_form metdou çağır ?>">tıklayınız.</a></p>
                </div>
            <?php } ?>
		</div><!-- .widget -->
	    </div>
</div>