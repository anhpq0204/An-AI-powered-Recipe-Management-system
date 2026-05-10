<?php $adminCurrentPage = basename($_SERVER['PHP_SELF']); ?>
<aside>
    <div id="sidebar" class="nav-collapse">
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="<?php if($adminCurrentPage == 'dashboard.php') echo 'active';?>" href="dashboard.php">
                        <i class="fa fa-dashboard"></i>
                        <span><?php _e('Dashboard'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="<?php if($adminCurrentPage == 'reg-users.php') echo 'active';?>" href="reg-users.php">
                        <i class="fa fa-users"></i>
                        <span><?php _e('Registered Users'); ?></span>
                    </a>
                </li>
                <li>
                    <a class="<?php if(in_array($adminCurrentPage, ['listed-recipes.php','edit-recipe.php','user-recipes.php'])) echo 'active';?>" href="listed-recipes.php">
                        <i class="fa fa-cutlery"></i>
                        <span><?php _e('Listed Recipes'); ?></span>
                    </a>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-comment"></i>
                        <span><?php _e('Comments'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($adminCurrentPage == 'new-comments.php') echo 'active';?>" href="new-comments.php"><?php _e('New'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'approved-comments.php') echo 'active';?>" href="approved-comments.php"><?php _e('Approved'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'rejected-comments.php') echo 'active';?>" href="rejected-comments.php"><?php _e('Rejected'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'all-comments.php') echo 'active';?>" href="all-comments.php"><?php _e('All'); ?></a></li>
                    </ul>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-bullhorn"></i>
                        <span><?php _e('Enquiry'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($adminCurrentPage == 'unreadenq.php') echo 'active';?>" href="unreadenq.php"><?php _e('Unread'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'readenq.php') echo 'active';?>" href="readenq.php"><?php _e('Read'); ?></a></li>
                    </ul>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-file-text"></i>
                        <span><?php _e('Pages'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($adminCurrentPage == 'about-us.php') echo 'active';?>" href="about-us.php"><?php _e('About Us'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'contact-us.php') echo 'active';?>" href="contact-us.php"><?php _e('Contact Us'); ?></a></li>
                    </ul>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;">
                        <i class="fa fa-bar-chart"></i>
                        <span><?php _e('Reports'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($adminCurrentPage == 'report-reg-users.php') echo 'active';?>" href="report-reg-users.php"><?php _e('Registered Users'); ?></a></li>
                        <li><a class="<?php if($adminCurrentPage == 'recipes-report.php') echo 'active';?>" href="recipes-report.php"><?php _e('Listed Recipes'); ?></a></li>
                    </ul>
                </li>

                <li>
                    <a class="<?php if($adminCurrentPage == 'search.php') echo 'active';?>" href="search.php">
                        <i class="fa fa-search"></i>
                        <span><?php _e('Search Recipes'); ?></span>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
