/**
 * Frontend Dashboard Gutenberg Blocks Editor Script
 *
 * @package Frontend Dashboard
 */

(function (wp) {
	if (!wp || !wp.blocks || !wp.element || !wp.blockEditor) {
		return;
	}

	const { registerBlockType } = wp.blocks;
	const { createElement: el, Fragment } = wp.element;
	const { InspectorControls, InnerBlocks } = wp.blockEditor || wp.editor;
	const { PanelBody, SelectControl, TextControl, ToggleControl, Placeholder } = wp.components;
	const { __ } = wp.i18n;

	const config = window.fedBlocksConfig || { userRoles: [] };

	// 1. FED Dashboard Block
	registerBlockType('fed/dashboard', {
		title: __('Frontend Dashboard', 'frontend-dashboard'),
		description: __('Renders the complete member dashboard with menus, profile tabs, and widgets.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'dashboard',
		keywords: [__('dashboard', 'frontend-dashboard'), __('frontend', 'frontend-dashboard'), __('profile', 'frontend-dashboard')],
		supports: {
			align: ['wide', 'full'],
		},
		attributes: {
			align: { type: 'string', default: 'full' },
			theme: { type: 'string', default: 'modern' },
			layout: { type: 'string', default: 'full' },
			default_tab: { type: 'string', default: '' },
		},
		edit: function (props) {
			const { attributes, setAttributes } = props;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Dashboard Settings', 'frontend-dashboard'), initialOpen: true },
						el(SelectControl, {
							label: __('Layout Mode', 'frontend-dashboard'),
							value: attributes.layout,
							options: [
								{ label: __('Full Width (Edge-to-edge)', 'frontend-dashboard'), value: 'full' },
								{ label: __('Container (Constrained Box)', 'frontend-dashboard'), value: 'container' },
							],
							onChange: function (val) {
								setAttributes({ layout: val });
							},
						}),
						el(TextControl, {
							label: __('Default Tab Slug (Optional)', 'frontend-dashboard'),
							value: attributes.default_tab,
							help: __('Leave blank to load the user profile default tab.', 'frontend-dashboard'),
							onChange: function (val) {
								setAttributes({ default_tab: val });
							},
						})
					)
				),
				el(
					Placeholder,
					{
						icon: 'dashboard',
						label: __('Frontend Member Dashboard', 'frontend-dashboard'),
						instructions: __('This block dynamically renders the user dashboard interface on the frontend with sidebar navigation, profile editor, and shortcode tabs.', 'frontend-dashboard'),
					},
					el(
						'div',
						{
							style: {
								padding: '12px 16px',
								background: '#f8fafc',
								border: '1px dashed #cbd5e1',
								borderRadius: '8px',
								width: '100%',
								fontSize: '13px',
								color: '#475569',
							},
						},
						el('strong', null, __('Active Shortcode Equivalent:', 'frontend-dashboard') + ' '),
						'[fed_dashboard]'
					)
				)
			);
		},
		save: function () {
			return null; // Dynamic block rendered on server
		},
	});

	// 2. FED Login & Registration Block
	registerBlockType('fed/login', {
		title: __('Frontend Login & Register', 'frontend-dashboard'),
		description: __('Renders modern Sign In, Registration, and Password Reset cards.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'admin-users',
		keywords: [__('login', 'frontend-dashboard'), __('register', 'frontend-dashboard'), __('auth', 'frontend-dashboard'), __('password', 'frontend-dashboard')],
		supports: {
			align: ['wide', 'full'],
		},
		attributes: {
			align: { type: 'string', default: '' },
			view: { type: 'string', default: 'tabs' },
			redirect_url: { type: 'string', default: '' },
		},
		edit: function (props) {
			const { attributes, setAttributes } = props;

			const viewLabels = {
				tabs: __('All-in-One Auth Card (Sign In, Register, Forgot Password Tabs)', 'frontend-dashboard'),
				login_only: __('Sign In Form Only', 'frontend-dashboard'),
				register_only: __('Registration Form Only', 'frontend-dashboard'),
				forgot_password_only: __('Password Reset Form Only', 'frontend-dashboard'),
			};

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Authentication Card Settings', 'frontend-dashboard'), initialOpen: true },
						el(SelectControl, {
							label: __('Display Mode', 'frontend-dashboard'),
							value: attributes.view,
							options: [
								{ label: __('All-in-One Tabs (Login + Register + Forgot Password)', 'frontend-dashboard'), value: 'tabs' },
								{ label: __('Login Form Only', 'frontend-dashboard'), value: 'login_only' },
								{ label: __('Register Form Only', 'frontend-dashboard'), value: 'register_only' },
								{ label: __('Forgot Password Form Only', 'frontend-dashboard'), value: 'forgot_password_only' },
							],
							onChange: function (val) {
								setAttributes({ view: val });
							},
						}),
						el(TextControl, {
							label: __('Redirect URL (Optional)', 'frontend-dashboard'),
							value: attributes.redirect_url,
							help: __('Target URL after successful login (defaults to dashboard).', 'frontend-dashboard'),
							onChange: function (val) {
								setAttributes({ redirect_url: val });
							},
						})
					)
				),
				el(
					Placeholder,
					{
						icon: 'admin-users',
						label: __('Frontend Authentication Portal', 'frontend-dashboard'),
						instructions: viewLabels[attributes.view] || viewLabels.tabs,
					},
					el(
						'div',
						{
							style: {
								padding: '12px 16px',
								background: '#f8fafc',
								border: '1px dashed #cbd5e1',
								borderRadius: '8px',
								width: '100%',
								fontSize: '13px',
								color: '#475569',
							},
						},
						el('strong', null, __('Mode:', 'frontend-dashboard') + ' '),
						attributes.view === 'tabs'
							? '[fed_login]'
							: attributes.view === 'login_only'
							? '[fed_login_only]'
							: attributes.view === 'register_only'
							? '[fed_register_only]'
							: '[fed_forgot_password_only]'
					)
				)
			);
		},
		save: function () {
			return null;
		},
	});

	// 3. FED Transactions Block
	registerBlockType('fed/transactions', {
		title: __('Transactions & Invoices', 'frontend-dashboard'),
		description: __('Displays member payment history, receipt downloads, and transaction status.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'cart',
		keywords: [__('transactions', 'frontend-dashboard'), __('payment', 'frontend-dashboard'), __('invoice', 'frontend-dashboard')],
		attributes: {
			per_page: { type: 'number', default: 10 },
		},
		edit: function (props) {
			const { attributes, setAttributes } = props;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Transaction Table Settings', 'frontend-dashboard'), initialOpen: true },
						el(TextControl, {
							label: __('Records Per Page', 'frontend-dashboard'),
							type: 'number',
							value: attributes.per_page,
							onChange: function (val) {
								setAttributes({ per_page: parseInt(val, 10) || 10 });
							},
						})
					)
				),
				el(
					Placeholder,
					{
						icon: 'cart',
						label: __('Member Transactions & Invoices', 'frontend-dashboard'),
						instructions: __('Renders a modern transaction history table with live status pills and receipt download triggers.', 'frontend-dashboard'),
					},
					el(
						'div',
						{
							style: {
								padding: '10px 14px',
								background: '#f8fafc',
								border: '1px dashed #cbd5e1',
								borderRadius: '8px',
								width: '100%',
								fontSize: '13px',
							},
						},
						'[fed_transactions]'
					)
				)
			);
		},
		save: function () {
			return null;
		},
	});

	// 4. FED User Role Content Block (Container)
	registerBlockType('fed/user-role', {
		title: __('Role-Restricted Content', 'frontend-dashboard'),
		description: __('Restricts visibility of nested blocks to specific user roles or guests.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'lock',
		keywords: [__('role', 'frontend-dashboard'), __('permission', 'frontend-dashboard'), __('restrict', 'frontend-dashboard'), __('access', 'frontend-dashboard')],
		attributes: {
			role: { type: 'string', default: 'subscriber' },
		},
		edit: function (props) {
			const { attributes, setAttributes } = props;

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: __('Access Control Settings', 'frontend-dashboard'), initialOpen: true },
						el(SelectControl, {
							label: __('Visible Only To', 'frontend-dashboard'),
							value: attributes.role,
							options: config.userRoles || [
								{ label: 'Guests (Logged-Out)', value: 'guest' },
								{ label: 'Subscribers', value: 'subscriber' },
								{ label: 'Authors', value: 'author' },
								{ label: 'Editors', value: 'editor' },
								{ label: 'Administrators', value: 'administrator' },
							],
							onChange: function (val) {
								setAttributes({ role: val });
							},
						})
					)
				),
				el(
					'div',
					{
						style: {
							border: '2px dashed #93c5fd',
							borderRadius: '12px',
							padding: '16px',
							background: '#eff6ff',
						},
					},
					el(
						'div',
						{
							style: {
								fontSize: '12px',
								fontWeight: 'bold',
								color: '#1d4ed8',
								marginBottom: '12px',
								display: 'flex',
								alignItems: 'center',
							},
						},
						el('span', { className: 'dashicons dashicons-lock', style: { marginRight: '6px' } }),
						__('Visible Only To:', 'frontend-dashboard') + ' ' + (attributes.role || 'subscriber')
					),
					el(InnerBlocks)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});

	// 5. FED Social Connect Block
	registerBlockType('fed/social-connect', {
		title: __('Social Login Buttons', 'frontend-dashboard'),
		description: __('1-Click OAuth Social Sign-In buttons with Google, Facebook, Apple, and more.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'networking',
		keywords: [__('social', 'frontend-dashboard'), __('oauth', 'frontend-dashboard'), __('google login', 'frontend-dashboard')],
		attributes: {
			layout: { type: 'string', default: 'grid' },
		},
		edit: function () {
			return el(
				Placeholder,
				{
					icon: 'networking',
					label: __('1-Click Social Sign-In Buttons', 'frontend-dashboard'),
					instructions: __('Displays social network authentication buttons configured in Frontend Dashboard settings.', 'frontend-dashboard'),
				},
				el(
					'div',
					{
						style: {
							padding: '10px 14px',
							background: '#f8fafc',
							border: '1px dashed #cbd5e1',
							borderRadius: '8px',
							width: '100%',
							fontSize: '13px',
						},
					},
					'[fed_social_connect]'
				)
			);
		},
		save: function () {
			return null;
		},
	});

	// 6. FED User Management Block
	registerBlockType('fed/user-management', {
		title: __('Frontend User Management', 'frontend-dashboard'),
		description: __('Searchable frontend user table for managers and administrators.', 'frontend-dashboard'),
		category: 'frontend-dashboard',
		icon: 'groups',
		keywords: [__('users', 'frontend-dashboard'), __('management', 'frontend-dashboard'), __('table', 'frontend-dashboard')],
		attributes: {
			per_page: { type: 'number', default: 15 },
		},
		edit: function () {
			return el(
				Placeholder,
				{
					icon: 'groups',
					label: __('Frontend User Management Table', 'frontend-dashboard'),
					instructions: __('Renders a frontend user search, filter, and management table for authorized roles.', 'frontend-dashboard'),
				},
				el(
					'div',
					{
						style: {
							padding: '10px 14px',
							background: '#f8fafc',
							border: '1px dashed #cbd5e1',
							borderRadius: '8px',
							width: '100%',
							fontSize: '13px',
						},
					},
					'[fed_user_management]'
				)
			);
		},
		save: function () {
			return null;
		},
	});
})(window.wp);
