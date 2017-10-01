<?php if ( !defined('CP_AUTH_INCLUDE') ) { echo 'Direct access not allowed.'; exit; } ?>
</p>
<link href="<?php echo plugins_url('css/stylepublic.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<link href="<?php echo plugins_url('css/cupertino/jquery-ui-1.8.20.custom.css', __FILE__); ?>" type="text/css" rel="stylesheet" />
<form class="cpp_form" name="cp_easyform_pform<?php echo $CP_EFB_global_form_count; ?>" id="cp_easyform_pform<?php echo $CP_EFB_global_form_count; ?>" action="<?php cp_easyform_get_site_url(); ?>" method="post" enctype="multipart/form-data" onsubmit="return cp_easyform_pform_doValidate<?php echo $CP_EFB_global_form_count; ?>(this);"><input type="hidden" name="cp_pform_psequence" value="<?php echo $CP_EFB_global_form_count; ?>" /><input type="hidden" name="cp_easyform_pform_process" value="1" /><input type="hidden" name="cp_easyform_id" value="<?php echo $id; ?>" /><input type="hidden" name="cp_ref_page" value="<?php esc_attr(cp_easyform_get_site_url()); ?>" /><input type="hidden" name="form_structure<?php echo $CP_EFB_global_form_count; ?>" id="form_structure<?php echo $CP_EFB_global_form_count; ?>" size="180" value="<?php echo str_replace('"','&quot;',str_replace("\r","",str_replace("\n","",esc_attr(cp_easyform_cleanJSON(cp_easyform_get_option('form_structure', CP_EASYFORM_DEFAULT_form_structure,$id)))))); ?>" />
<div id="fbuilder">
    <div id="fbuilder<?php echo $CP_EFB_global_form_count; ?>">
        <div id="formheader<?php echo $CP_EFB_global_form_count; ?>"></div>
        <div id="fieldlist<?php echo $CP_EFB_global_form_count; ?>"></div>
    </div>
</div>
<div id="cpcaptchalayer<?php echo $CP_EFB_global_form_count; ?>">
<?php if (cp_easyform_get_option('cv_enable_captcha', CP_EASYFORM_DEFAULT_cv_enable_captcha,$id) != 'false') { ?>
  <?php _e("Please enter the security code:"); ?><br />
  <img src="<?php echo plugins_url('/captcha/captcha.php?ps='.$CP_EFB_global_form_count.'&width='.cp_easyform_get_option('cv_width', CP_EASYFORM_DEFAULT_cv_width,$id).'&height='.cp_easyform_get_option('cv_height', CP_EASYFORM_DEFAULT_cv_height,$id).'&letter_count='.cp_easyform_get_option('cv_chars', CP_EASYFORM_DEFAULT_cv_chars,$id).'&min_size='.cp_easyform_get_option('cv_min_font_size', CP_EASYFORM_DEFAULT_cv_min_font_size,$id).'&max_size='.cp_easyform_get_option('cv_max_font_size', CP_EASYFORM_DEFAULT_cv_max_font_size,$id).'&noise='.cp_easyform_get_option('cv_noise', CP_EASYFORM_DEFAULT_cv_noise,$id).'&noiselength='.cp_easyform_get_option('cv_noise_length', CP_EASYFORM_DEFAULT_cv_noise_length,$id).'&bcolor='.cp_easyform_get_option('cv_background', CP_EASYFORM_DEFAULT_cv_background,$id).'&border='.cp_easyform_get_option('cv_border', CP_EASYFORM_DEFAULT_cv_border,$id).'&font='.cp_easyform_get_option('cv_font', CP_EASYFORM_DEFAULT_cv_font,$id), __FILE__); ?>"  id="captchaimg<?php echo $CP_EFB_global_form_count; ?>" alt="security code" border="0"  /><br />
  <?php _e("Security Code:"); ?><div class="dfield"><input type="text" size="20" name="hdcaptcha" id="hdcaptcha<?php echo $CP_EFB_global_form_count; ?>" value="" /><div class="cpefb_error message" id="hdcaptcha_error<?php echo $CP_EFB_global_form_count; ?>" generated="true" style="display:none;position: absolute; left: 0px; top: 25px;"><?php echo esc_attr(cp_easyform_get_option('cv_text_enter_valid_captcha', CP_EASYFORM_DEFAULT_cv_text_enter_valid_captcha,$id)); ?></div></div>  
<?php } ?>
</div>
<div id="cp_subbtn<?php echo $CP_EFB_global_form_count; ?>" class="cp_subbtn"><?php _e($button_label); ?></div>
</form>