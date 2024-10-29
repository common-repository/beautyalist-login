<?php

namespace BeautyAListLogin\Services;

class ViewService
{
    public function get_admin_page($api_key, $client_id, $page, $settings_error, $system_page, $role, $roles)
    {
        $show_second_tab = false;
        if (false == $settings_error['error']) {
            if ($api_key && $client_id) {
                $show_second_tab = true;
            }
        }

        $this->get_header();

        ?>
        <div class="bootstrap-bal-l">
            <div class="Bl-Page__Content">                
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="settings-tab" data-bs-toggle="tab" data-bs-target="#settings-tab-pane" type="button" role="tab" aria-controls="settings-tab-pane" aria-selected="true">Settings</button>
                    </li>
                    <?php if ($show_second_tab) { ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service-tab-pane" type="service" role="tab" aria-controls="button-tab-pane" aria-selected="false">BeautyAList Button</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="additional-tab" data-bs-toggle="tab" data-bs-target="#additional-tab-pane" type="service" role="tab" aria-controls="additional-tab-pane" aria-selected="false">Additional settings</button>
                        </li>
                        
                    <?php } ?>
                </ul>
                <!-- Nav content -->
                <div class="tab-content" id="myTabContent">
                    <div class="tab-pane show active" id="settings-tab-pane" role="tabpanel" aria-labelledby="settings-tab" tabindex="0">
                        <?php $this->first_tab($api_key, $client_id, $settings_error); ?>
                    </div>

                    <?php if ($show_second_tab) { ?>
                        <div class="tab-pane" id="service-tab-pane" role="tabpanel" aria-labelledby="service-tab" tabindex="0">
                            <?php $this->second_tab($api_key, $client_id, $system_page, $page); ?>
                        </div>
                        <div class="tab-pane" id="additional-tab-pane" role="tabpanel" aria-labelledby="additional-tab" tabindex="0">
                            <?php $this->third_tab($role, $roles); ?>
                        </div>
                    <?php } ?>        
                </div>
                <br>
                <a target="_blank" href="https://licensify.io/pages/Universal-Terms-of-Service">Terms and Conditions</a>
                |
                <a target="_blank" href="https://licensify.io/pages/privacy-policy">Privacy Policy</a>
            </div>
        <?php

        $this->get_footer();
    }

    private function first_tab($api_key = '', $client_id = '', $settings_error)
    {
        ?>
            <div class="Bl-Card card card-mc">
                <div class="Bl-Card__Section card-body">
                    <div class="mb-3">
                        <div class="form-group">
                            <label for="formClientID">Client ID</label>
                            <input id="formClientID" class="form-control" type="text" data-element="id" placeholder="Input your key here" value="<?php echo esc_attr($client_id); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-group">
                            <label for="formAPIkey">API key</label>
                            <input id="formAPIkey" class="form-control" type="text" data-element="key" placeholder="Input your API key here" value="<?php echo esc_attr($api_key); ?>">
                        </div>
                    </div>
                    <div class="text-left mb-3">
                        <button type="submit" class="blist__button blist__button--primary" data-nonce="<?php echo esc_attr(wp_create_nonce('bl-login-save-key-nonce')); ?>" data-element="bl_login_save_key" data-href="<?php echo esc_url(admin_url('admin-post.php')); ?>" value="">Save</button>
                    </div>
                    <div data-element="bl_login_save_key_message">
                        <div class="mb-3">
                            <?php if (true == $settings_error['error']) { ?>
                               <p class="text-danger">Invalid data environment specified.</p>
                               <p>Please enter correct ClientID and API key or contact <a href="mailto:info@beauticianlist.com">info@beauticianlist.com</a></p>
                            <?php } ?>

                            <?php if ($settings_error['message']) { ?>
                                <p><?php echo esc_attr($settings_error['message']); ?>
                            <?php } ?>

                            <?php if ($api_key && $client_id) { ?>
                                <p>You can manage you key details as well as update / remove your Brand profile here
                                    <a target="_blank" href="https://dashboard.licensify.io/">https://dashboard.licensify.io/</a>.</p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    public function generate_new_page($id, $page)
    {
        $embed = $this->get_embed($id, $page);
        $str   = '
<!-- wp:html -->
<h2 class="">Seamless Sign-In for Beauty Professionals</h2>
<!-- /wp:html -->

<!-- wp:group {"style":{"position":{"type":""}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"} -->
<div class="wp-block-group">
<!-- wp:html -->'.$embed.'<!-- /wp:html -->
</div>
<!-- /wp:group -->

<!-- wp:html -->
<p>With BeautyAList, beauty professionals can now access professional-only products and content more easily than ever.</p>
<h2>Key features of the BeautyAList account include:</h2>
<!-- /wp:html -->

<!-- wp:columns -->
<div class="wp-block-columns">
<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html --><h3>Unified Access</h3>
<p>A single BeautyAList account grants entry to various professional beauty websites.</p>
<!-- /wp:html -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html -->
<h3>Exclusive Perks</h3>

<p>Professionals in the beauty industry can easily access pro-only products and resources.</p>
<!-- /wp:html -->
</div>
<!-- /wp:column -->

<!-- wp:column -->
<div class="wp-block-column">
<!-- wp:html -->
<h3>Enhanced Security</h3>
<p>The security and privacy of user information are safeguarded with advanced measures.</p>
<!-- /wp:html -->
</div>
<!-- /wp:column -->
</div>
<!-- /wp:columns -->

<!-- wp:html -->
<h2>How It Works:</h2>
<ul>
<li>Simple Sign-In: Sign in with your BeautyAList account or create one in seconds.</li>
<li>Make sure to give permission to link your account. It\'s a quick, one-time process.</li>
<li>Start shopping!</li>
</ul>
<!-- /wp:html -->

<!-- wp:html -->
<h2>Need Help or Have Questions?</h2>
<p>Our dedicated support team is here to assist you. Whether you have queries or need guidance, we\'re just a message away! '.
'<a href="mailto:hello@licensify.io">Send us a message</a> and we will get back to you ASAP.</p>
<!-- /wp:html -->';

        return $str;
    }

    public function get_embed($id, $page)
    {
        return '<script type="text/javascript" src="https://beautyalist.com/embed/button/v1.js" async></script>'.
            '<a class="blist__openid__button blist__openid__button--default" data-client_id="'.$id.'" '.
            'data-redirect_url="'.$page.'">Proceed with BeautyAList</a>';
    }

    private function second_tab($key, $id, $system_page, $page)
    {
        ?>
            <div class="Bl-Card card card-mc">
                <div class="Bl-Card__Section card-body">
                    <h6 class="card-title">Add BeautyAList OpenID button on your store</h6>
                    <p>When a person lands on the page where the button code is added, he/she can see the "Proceed with BeautyAList" button. Clicking a button will start the license verification process. After successful license verification, the user is brought back to your shopify website, with the shopify user account being automatically created, and the user being auto logged in. The newly registered user has a "beautyalist" tag assigned, as well as the license data added to the notes.</p>
                    <p>Please find below the ways to add the button in your store.</p>
                
                    <ul class="nav nav-tabs" id="myTab2" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="button-tab" data-bs-toggle="tab" data-bs-target="#button" type="button" role="tab" aria-controls="button" aria-selected="true">BeautyAList OpenID button code</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="page-tab" data-bs-toggle="tab" data-bs-target="#page" type="button" role="tab" aria-controls="page" aria-selected="true">BeautyAList Page</button>
                        </li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane active" id="button" role="tabpanel" aria-labelledby="button-tab">
                            <div class="Bl-Card card card-mc">
                                <div class="Bl-Card__Section card-body">
                                    <div>
                                        <div class="row">
                                            <div class="colbl-5">
                                                <h6 class="card-title">Button Embed</h6>
                                                <p>Embed BeautyAlist button anywhere on your website.</p>
                                                <p>Common embed places - Registration and Login pages, Cart page.</p>
                                            </div>
                                            <div class="colbl-7">
                                                <div class="shadow-sm p-3 bg-white rounded">
                                                    <div class="mb-4">
                                                        <textarea data-element="bl_login_button_result"  readonly rows="7" class="form-control"><script type="text/javascript" src="https://beautyalist.com/embed/button/v1.js" async></script><a class="blist__openid__button blist__openid__button--default" data-client_id="<?php echo esc_attr($id); ?>" data-redirect_url="<?php echo esc_url($system_page); ?>">Proceed with BeautyAList</a></textarea>
                                                    </div>
                                                    <div>
                                                        <div class="text-left mb-3">
                                                            <button data-element="bl_login_button_copy" data-target="bl_login_button_result" class="blist__button blist__button--primary">Copy</button>
                                                        </div>
                                                        <p>Please add the code exactly as is provided. Removing any part of it may cause plugin to not work properly.</p>
                                                        <p> <a target="_blank" rel="noopener noreferrer" href="mailto:hello@licensify.io">Contact us</a> in case you have any questions or issues.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="tab-pane" id="page" role="tabpanel" aria-labelledby="page-tab">
                            <div class="Bl-Card card card-mc">
                                <div class="Bl-Card__Section card-body">
                                    <div>
                                        <div class="row">
                                            <div class="colbl-5">
                                                <h6 class="card-title">Create a Page</h6>
                                                <p>A dedicated BeautyAList page to describe the benefits of license verification via BeautyAList.</p>
                                                <p>Click CREATE button at the right and the page with predefined content will be automatically created in your store.</p>
                                            </div>
                                            <div class="colbl-7">
                                                <?php $this->get_second_tab_page($page); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    private function third_tab($role, $roles)
    {
        ?>
        <div class="Bl-Card card card-mc">
            <div class="Bl-Card__Section card-body">
                <div class="mb-3">
                    <div class="form-group">
                        <label for="formRole">Role:</label></br>
                        <select id="formRole" class="form-select form-control" data-element="role" aria-label="select">
                            <?php foreach ($roles as $key => $value) { ?>
                                <option <?php if ($key == $role) {
                                    echo 'selected';
                                } ?> value="<?php echo $key; ?> "><?php echo $value['name']; ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <p>Select what role you want the pros to be registered under.</p>
                <div class="text-left mb-3">
                    <button type="submit" class="blist__button blist__button--primary" data-nonce="<?php echo esc_attr(wp_create_nonce('bl-login-save-role-nonce')); ?>" data-element="bl_login_save_role" data-href="<?php echo esc_url(admin_url('admin-post.php')); ?>" value="">Save</button>
                </div>
                <div data-element="bl_login_save_role_message">
                    <div class="mb-3">
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

    public function get_second_tab_page($page)
    {
        $page_title = get_the_title($page);
        if ($page && $page_title) { ?>
                <div class="alert alert-primary">
                    <div class="row">
                        <div class="colbl-md-9">
                            <div>
                                <h6 class="card-title">Your page is live</h6>
                            </div>
                            <div>
                                <p>You can view your page here:<br>
                                <a href="<?php echo esc_url(get_page_link($page)); ?>" target="_blank"><?php echo esc_attr($page_title); ?></a></p>
                            </div>
                        </div>
                        <div class="colbl-md-3 d-flex justify-content-end">
                            <div class="dropdown">
                                <button class="blist__button blist__button--primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Actions</button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="<?php echo esc_url(get_page_link($page)); ?>">View page</a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url(get_edit_post_link($page)); ?>">Edit settings</a></li>
                                    <li><a class="dropdown-item" href="<?php echo esc_url(get_delete_post_link($page, '', true)); ?>">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="shadow-sm p-3 bg-white rounded">
                    <div class="row">
                        <div class="col">
                            <h6 class="card-title">Link to your page</h6>
                            <p>Now that you've created your <?php echo esc_attr($page_title); ?>  page, link it from your website. You can easily <!--
                            -->add a link to your header or footer. Just copy the page url below and click the button to go to <!--
                            -->your navigation menu. Select the menu you want and then click "Add menu item".</p>
                            <div class="">
                                <input class="form-control" data-element="bl_login_page_url" disabled=""  value="<?php echo esc_url(get_page_link($page)); ?>">
                            </div>
                        </div>
                    </div>
                    <br>
                    <button class="blist__button blist__button--primary" data-element="bl_login_button_copy" data-target="bl_login_page_url"  type="button">Copy</button>
                    <br>
                    <br>
                    <p>Need help? <a target="_blank" rel="noopener noreferrer" href="mailto:hello@licensify.io">Please contact us.</a></p>
                </div>
        <?php } else { ?>
                <div class="row">
                    <div class="colbl-md-9 ml-n3">
                        <h6 class="card-title">Create a Dedicated Discount Page</h6>
                    </div>
                    <div class="colbl-md-3 d-flex justify-content-end">
                        <div class="text-right mb-3">
                            <button class="blist__button blist__button--primary" data-element="bl_login_create_page" data-href="<?php echo esc_url(admin_url('admin-post.php')); ?>" value="">Create</button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <h6 class="card-title">How does this page work?</h6>
                    <p>When you click "CREATE", the plugin will automatically create the new page in your store. Page has minimum styling to look good on both desktop and mobile.</p>
                    <p>After you create the page, it will be available in the page menu in your store admin.</p>
                    <p>The page comes with predefined content, but you can update it any time to any extent. Just make sure that the BeautyAList embed button code remains intact.</p>
                    <p>You can also unpublish or delete the page any time.</p>
                    <p>Need help? <a target="_blank" rel="noopener noreferrer" href="mailto:hello@licensify.io">Please contact us.</a></p>
                </div>
            <?php
        }
    }

    public function get_profile_field($licenses, $tag)
    {
        ?>
            <h3>Licensify Information</h3> 
            <table class="form-table">
                <tr>
                    <th>
                        <label for="bll_data">Licenses</label>
                    </th>
                    <td>
                        <textarea rows="5" name="bll_data" id="bll_data" class="regular-text" readonly><?php echo esc_attr($licenses); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th>
                        <label for="bll_tag">Tag</label>
                    </th>
                    <td>
                        <input type="text" name="bll_tag" id="bll_tag" value="<?php echo esc_attr($tag); ?>" class="regular-text"/>
                        <input type="hidden" name="bll_tag_nonce" value="<?php echo esc_attr(wp_create_nonce('bl-login-tag-nonce')); ?>">
                    </td>
                </tr>
            </table>
        <?php

        return $str;
    }

    public function get_popup()
    {
        ?>
            <div class="bootstrap-bal-l">
                <div class="modal fade bl_login_popup blist__wrapper" id="bl_login_popup" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="blist__dialog modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body m-0 p-0">
                                <div class="blist__dialog__main container-fluid">
                                    <header class="blist__dialog__header modal-header">
                                        <div class="blist__dialog__close" data-bs-dismiss="modal" aria-label="Close"></div>
                                    </header>

                                    <div data-element="bl_login_intro_div">
                                        <div class="blist__form__header">
                                            <p>It looks like you already have a profile set up with <?php echo esc_attr(get_bloginfo('name')); ?>; please use your credentials below to log in.</p>
                                            <form method="POST">
                                                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('bl-login-login')); ?>">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <input class="form-control" placeholder="Email *" autocomplete="new-password" type="text" name="email">
                                                    </div>
                                                    <div class="form-group">
                                                        <input class="form-control" placeholder="Password *" autocomplete="new-password" type="password" name="password">
                                                    </div>
                                                    <small class="invalid-message display-none" data-element="bl_login_login_error"></small>
                                                </div>
                                                <button data-href="<?php echo esc_url(admin_url('admin-ajax.php')); ?>" type="button" class="blist__button blist__button--primary is-large" data-element="bl_login_login">Sign in</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    public function get_page()
    { ?>
            <div class="bootstrap-bal-l">
                <div class="modal fade bl_login_page blist__wrapper" id="bl_login_page_popup" tabindex="-1" role="dialog" aria-hidden="true">
                    <div class="blist__dialog modal-dialog modal-lg modal-dialog-centered" role="document">
                        <div class="modal-content">
                            <div class="modal-body m-0 p-0">
                                <div class="blist__dialog__main container-fluid">
                                    <header class="blist__dialog__header modal-header">
                                        <div class="blist__dialog__close" data-dismiss="modal" aria-label="Close"></div>
                                        <h1>Create page</h1>
                                    </header>

                                    <div data-element="bl_login_intro_div">
                                        <div class="blist__form__header">
                                            <form method="POST">
                                                <input type="hidden" name="nonce" value="<?php echo esc_attr(wp_create_nonce('bl-login-create-page-nonce')); ?>">
                                                <div class="form-group">
                                                    <div class="form-group">
                                                        <input class="form-control" placeholder="Page name" type="text" name="page">
                                                    </div>
                                                    <small class="invalid-message display-none" data-element="bl_login_page_error"></small>
                                                </div>
                                                <button data-href="<?php echo esc_url(admin_url('admin-post.php')); ?>" type="button" class="blist__button blist__button--primary is-large" data-element="bl_login_page">Add page</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php
    }

    private function get_header()
    {
        ?>
            <div class="Bl-Plugin__Header">
                <div class="Bl-Plugin__Logo">
                    <span>
                        <img src="<?php echo esc_url(plugins_url('../../assets/img/logo.webp', __FILE__)); ?>">
                    </span>
                </div>
                <div class="Bl-Plugin__Title">
                    <h2 class="title">BeautyAList Login</h2>
                </div>
            </div>
        <?php
    }

    private function get_footer()
    {
        ?>
        </div>
        <?php
    }
}