<?php

if ( isset( $_REQUEST['fed_user_profile'] ) ) {
	fed_show_user_by_role($fed_user_attr, $_REQUEST['fed_user_profile']);
} else {
	fed_show_users_by_role( $fed_user_attr );
}
