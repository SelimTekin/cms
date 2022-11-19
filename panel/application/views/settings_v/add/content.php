<div class="row">

    <div class="col-md-12">
		<h4 class="m-b-lg">
            Site Ayarı Ekle
        </h4>
	</div>

    <div class="col-md-12">
        <form action="<?php echo base_url("settings/save"); ?>" method="post" enctype="multipart/form-data"><!-- dosya gönderdiğimiz için enctype="multipart/form-data" yapalım ki o bilgiyi alabilelim-->
            <div class="widget">
                <div class="m-b-lg nav-tabs-horizontal">
                    <!-- tabs list -->
                    <ul class="nav nav-tabs" role="tablist">
                        <li role="presentation" class="active"><a href="#tab-1" aria-controls="tab-1" role="tab" data-toggle="tab">Site Bilgileri</a></li>
                        <li role="presentation"><a href="#tab-2" aria-controls="tab-2" role="tab" data-toggle="tab">Adres</a></li>
                        <li role="presentation"><a href="#tab-3" aria-controls="tab-3" role="tab" data-toggle="tab">Hakkımızda</a></li>
                        <li role="presentation"><a href="#tab-4"  aria-controls="tab-4" role="tab" data-toggle="tab">Misyon</a></li>
                        <li role="presentation"><a href="#tab-5"  aria-controls="tab-5" role="tab" data-toggle="tab">Vizyon</a></li>
                        <li role="presentation"><a href="#tab-6"  aria-controls="tab-6" role="tab" data-toggle="tab">Sosyal Medya</a></li>
                        <li role="presentation"><a href="#tab-7"  aria-controls="tab-7" role="tab" data-toggle="tab">Logo</a></li>
                    </ul><!-- .nav-tabs -->

                    <!-- Tab panes -->
                    <div class="tab-content p-md">
                        <div role="tabpanel" class="tab-pane in active fade" id="tab-1">

                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>Şirket Adı</label>
                                    <input class="form-control" placeholder="Şirketinizin ya da Sitenizin adı" name="company_name" value="<?php echo isset($form_error) ? set_value("company_name") : ""; ?>">
                                    <?php if(isset($form_error)){ ?>
                                        <small class="pull-right input-form-error"> <?php echo form_error("company_name");?></small>
                                    <?php } ?>

                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Telefon 1</label>
                                    <input class="form-control" placeholder="Telefon numaranız" name="phone_1" value="<?php echo isset($form_error) ? set_value("phone_1") : ""; ?>">
                                    <?php if(isset($form_error)){ ?>
                                        <small class="pull-right input-form-error"> <?php echo form_error("phone_1");?></small>
                                    <?php } ?>

                                </div>

                                <div class="form-group col-md-4">
                                    <label>Telefon 2</label>
                                    <input class="form-control" placeholder="Diğer telefon numaranız (opsiyonel)" name="phone_2">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Fax 1</label>
                                    <input class="form-control" placeholder="Fax numaranız" name="fax_1">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Fax 2</label>
                                    <input class="form-control" placeholder="Diğer fax numaranız (opsiyonel)" name="fax_2">
                                </div>
                            </div>

                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-2">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Adres Bilgisi</label>
                                    <textarea name="address" class="m-0" data-plugin="summernote" data-options="{height: 250}"></textarea>
                                </div>
                            </div>
                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-3">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Hakkımızda</label>
                                    <textarea name="about_us" class="m-0" data-plugin="summernote" data-options="{height: 250}"></textarea>
                                </div>
                            </div>
                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-4">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Misyonumuz</label>
                                    <textarea name="mission" class="m-0" data-plugin="summernote" data-options="{height: 250}"></textarea>
                                </div>
                            </div>
                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-5">
                            <div class="row">
                                <div class="form-group col-md-12">
                                    <label>Vizyonumuz</label>
                                    <textarea name="vision" class="m-0" data-plugin="summernote" data-options="{height: 250}"></textarea>
                                </div>
                            </div>
                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-6">

                            <div class="row">
                                <div class="form-group col-md-8">
                                    <label>E-posta Adresiniz</label>
                                    <input class="form-control" placeholder="Şirketinize ait e-posta adresi" name="email" value="<?php echo isset($form_error) ? set_value("email") : ""; ?>">
                                    <?php if(isset($form_error)){ ?>
                                        <small class="pull-right input-form-error"> <?php echo form_error("email");?></small>
                                    <?php } ?>

                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Facebook</label>
                                    <input class="form-control" placeholder="Facebook Adresiniz" name="facebook">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Twitter</label>
                                    <input class="form-control" placeholder="Twitter Adresiniz" name="twitter">
                                </div>
                            </div>

                            <div class="row">
                                <div class="form-group col-md-4">
                                    <label>Instagram</label>
                                    <input class="form-control" placeholder="Instagram Adresiniz" name="instagram">
                                </div>

                                <div class="form-group col-md-4">
                                    <label>Linkedin</label>
                                    <input class="form-control" placeholder="Linkedin Adresiniz" name="linkedin">
                                </div>
                            </div>

                        </div><!-- .tab-pane  -->

                        <div role="tabpanel" class="tab-pane fade" id="tab-7">

                            <div class="row">
                                <div class="form-group image_upload_container">
                                    <label>Görsel Seçiniz</label>
                                    <input type="file" name="logo" class="form-control">
                                </div>
                            </div>

                        </div><!-- .tab-pane  -->

                    </div><!-- .tab-content  -->
                </div><!-- .nav-tabs-horizontal -->
            </div><!-- .widget -->
            <button type="submit" class="btn btn-primary btn-md">Kaydet</button>
            <a href="<?php echo base_url("settings"); ?>" class="btn btn-danger">İptal</a>
        </form>
    </div><!-- END column -->

</div>