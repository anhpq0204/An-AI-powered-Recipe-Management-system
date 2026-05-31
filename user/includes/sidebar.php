<?php $userCurrentPage = basename($_SERVER['PHP_SELF']); ?>
<aside>
    <div id="sidebar" class="nav-collapse">
        <div class="leftside-navigation">
            <ul class="sidebar-menu" id="nav-accordion">
                <li>
                    <a class="<?php if($userCurrentPage == 'dashboard.php') echo 'active';?>" href="dashboard.php">
                        <i class="fa fa-dashboard"></i>
                        <span><?php _e('Dashboard'); ?></span>
                    </a>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;" class="<?php if(in_array($userCurrentPage, ['add-recipe.php', 'manage-recipes.php', 'edit-recipe.php'])) echo 'active';?>">
                        <i class="fa fa-cutlery"></i>
                        <span><?php _e('List Your Recipe'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($userCurrentPage == 'add-recipe.php') echo 'active';?>" href="add-recipe.php"><?php _e('Add Recipe'); ?></a></li>
                        <li><a class="<?php if($userCurrentPage == 'manage-recipes.php') echo 'active';?>" href="manage-recipes.php"><?php _e('Manage Recipe'); ?></a></li>
                    </ul>
                </li>

                <li class="sub-menu">
                    <a href="javascript:;" class="<?php if(in_array($userCurrentPage, ['new-comments.php', 'approved-comments.php', 'rejected-comments.php', 'all-comments.php'])) echo 'active';?>">
                        <i class="fa fa-comment"></i>
                        <span><?php _e('Comments'); ?></span>
                    </a>
                    <ul class="sub">
                        <li><a class="<?php if($userCurrentPage == 'new-comments.php') echo 'active';?>" href="new-comments.php"><?php _e('New'); ?></a></li>
                        <li><a class="<?php if($userCurrentPage == 'approved-comments.php') echo 'active';?>" href="approved-comments.php"><?php _e('Approved'); ?></a></li>
                        <li><a class="<?php if($userCurrentPage == 'rejected-comments.php') echo 'active';?>" href="rejected-comments.php"><?php _e('Rejected'); ?></a></li>
                        <li><a class="<?php if($userCurrentPage == 'all-comments.php') echo 'active';?>" href="all-comments.php"><?php _e('All'); ?></a></li>
                    </ul>
                </li>

                <li>
                    <a class="<?php if($userCurrentPage == 'search.php') echo 'active';?>" href="search.php">
                        <i class="fa fa-search"></i>
                        <span><?php _e('Search'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="<?php if($userCurrentPage == 'meal-planner.php') echo 'active';?>" href="meal-planner.php">
                        <i class="fa fa-calendar"></i>
                        <span><?php _e('Meal Planner'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="<?php if(in_array($userCurrentPage, ['my-meal-plans.php', 'view-meal-plan.php'])) echo 'active';?>" href="my-meal-plans.php">
                        <i class="fa fa-list"></i>
                        <span><?php _e('My Plans'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="<?php if($userCurrentPage == 'my-favorites.php') echo 'active';?>" href="my-favorites.php">
                        <i class="fa fa-heart"></i>
                        <span><?php _e('My Favorites'); ?></span>
                    </a>
                </li>

                <li>
                    <a class="<?php if($userCurrentPage == 'notifications.php') echo 'active';?>" href="notifications.php">
                        <i class="fa fa-bell"></i>
                        <span><?php _e('Notifications'); ?></span>
                        <?php
                        $uid_sb = intval($_SESSION['frsuid']);
                        $nb = $con->prepare("SELECT COUNT(*) AS c FROM notifications WHERE user_id = ? AND is_read = 0");
                        $nb->bind_param("i", $uid_sb);
                        $nb->execute();
                        $unread = intval($nb->get_result()->fetch_assoc()['c']);
                        $nb->close();
                        if($unread > 0): ?>
                        <span class="badge bg-danger ms-1"><?php echo $unread; ?></span>
                        <?php endif; ?>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</aside>
