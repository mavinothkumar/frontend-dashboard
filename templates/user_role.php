<?php

if ( isset( $_REQUEST['user'] ) ) {
	fed_show_user_by_role($fed_user_attr, $_REQUEST['user']);
} else {
	fed_show_users_by_role( $fed_user_attr );
}
