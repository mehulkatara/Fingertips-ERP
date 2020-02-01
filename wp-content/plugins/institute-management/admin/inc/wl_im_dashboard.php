<?php
defined( 'ABSPATH' ) or die();
require_once( WL_IM_PLUGIN_DIR_PATH . '/admin/inc/helpers/WL_IM_Helper.php' );

$data                      = WL_IM_Helper::get_data();
$count                     = $data['count'];
$course_data               = $data['course_data'];
$recent_enquiries          = $data['recent_enquiries'];
$popular_courses_enquiries = $data['popular_courses_enquiries'];
$revenue                   = $data['revenue'];
?>
<div class="container-fluid wl_im_container">
    <!-- hide banner mehul
	<div class="row col-md-12 institute_banner">
		<div class="col-md-6 col-sm-12 institute_banner_img">
			<h2><?php esc_html_e('Institute Management Pro', WL_IM_DOMAIN ); ?></h2>
			<img class="img-fluid" src="<?php echo WL_IM_PLUGIN_URL . '/admin/img/institute.png' ?>">
		</div>
		<div class="col-md-6 col-sm-12 institute_banner_featurs">
			<h4 class="mb-3 ml-5"><span class="border-white border-bottom pb-1"><?php esc_html_e('Institute Management Pro Features', WL_IM_DOMAIN ); ?></span></h4>
			<ul>
				<li><?php esc_html_e('Course Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Batch Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Enquiry Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Student Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Fee Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Exam Results Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Staff Management', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Admin Dashboard', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Student Dashboard', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Access Control', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Generate and Print Reports', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Export records to Excel or PDF', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Search and Filter Records', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Institute Settings', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Print Fee Receipt, Report, ID Card, Admission Detail and Completion Certificate', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Institute Noticeboard Widget', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Send Notifications to Students', WL_IM_DOMAIN ); ?></li>
				<li><?php esc_html_e('Pay Fees with PayPal, Razorpay Payment Methods', WL_IM_DOMAIN ); ?></li>
			</ul>
			<div class="col-md-12">
				<a class="button-primary button-hero" href="http://demo.weblizar.com/institute-management-pro/" target="_blank"><?php esc_html_e('View Demo', WL_IM_DOMAIN ); ?></a>
				<a class="button-primary button-hero" href="https://weblizar.com/plugins/institute-management-pro/" target="_blank"><?php esc_html_e('Buy Now', WL_IM_DOMAIN ); ?> $15</a>
			</div>
			<div class="plugin_version">
				<span><b>2.4</b><?php esc_html_e('Version', WL_IM_DOMAIN ); ?></span>
			</div>
		</div>
	</div>
	hide banner mehul -->
	<!-- row 1 -->
	<div class="row">
		<div class="col">
			<!-- main header content -->
			<h1 class="display-4 text-center "><span class="border-bottom"><i class="fas fa-tachometer"></i> <?php esc_html_e( 'Dashboard', WL_IM_DOMAIN ); ?></span></h1>
			<div class="mt-3 alert alert-info text-center text-white blue blue-gradient" role="alert">
				<?php esc_html_e('Here, you can view statistics and reports.', WL_IM_DOMAIN ); ?>
			</div>
			<!-- end main header content -->
		</div>
	</div>
	<!-- end - row 1 -->

	<!-- row 2 -->
	<div class="row">
		<div class="card col">
			<div class="card-header blue-gradient">
				<!-- card header content -->
				<div class="row">
					<div class="col-xs-12 ">
						<div class="h3 text-white ml-3  "><?php esc_html_e( 'View Statistics and Reports', WL_IM_DOMAIN ); ?></div>
					</div>
				</div>
				<!-- end - card header content -->
			</div>
			<div class="card-body">
				<!-- card body content -->
				<div class="row text-center">
					<?php
					if ( current_user_can( 'wl_im_manage_courses' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-graduation-cap"></i> 
									<a href="<?php menu_page_url( 'institute-management-courses' ); ?>" class="text-white">
										<?php esc_html_e( 'Courses', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Total Courses', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html( $count->courses ); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Active Courses', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->courses_active); ?></span>
								</li>
							</ul>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_batches' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group ">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-object-group"></i> 
									<a href="<?php menu_page_url( 'institute-management-batches' ); ?>" class="text-white">
										<?php esc_html_e( 'Batches', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Total Batches', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->batches); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Active Batches', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->batches_active); ?></span>
								</li>
							</ul>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_enquiries' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-envelope"></i> 
									<a href="<?php menu_page_url( 'institute-management-enquiries' ); ?>" class="text-white">
										<?php esc_html_e( 'Enquiries', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Total Enquiries', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->enquiries); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Active Enquiries', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->enquiries_active); ?></span>
								</li>
							</ul>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_fees' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-dollar-sign"></i> 
									<a href="<?php menu_page_url( 'institute-management-fees' ); ?>" class="text-white">
										<?php esc_html_e( 'Installments', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Total Installments', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->installments); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Revenue', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($revenue); ?></span>
								</li>
							</ul>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_fees' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-dollar-sign"></i> 
									<a href="<?php menu_page_url( 'institute-management-fees' ); ?>" class="text-white">
										<?php esc_html_e( 'Fees', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Fees Pending', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students_fees_pending); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Fees Paid', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students_fees_paid); ?></span>
								</li>
							</ul>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_students' ) ) { ?>
						<div class="col-md-3 col-sm-4 col-xs-2 mb-4">
							<ul class="list-group">
								<li class="list-group-item active h5 blue-gradient"><i class="fas fa-users"></i> 
									<a href="<?php menu_page_url( 'institute-management-students' ); ?>" class="text-white">
										<?php esc_html_e( 'Students', WL_IM_DOMAIN ); ?>
									</a>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Total Students', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Current Students', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students_current); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Former Students', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students_former); ?></span>
								</li>
								<li class="list-group-item h6">
									<span class="text-secondary"><?php esc_html_e( 'Students Discontinued', WL_IM_DOMAIN ); ?>:</span>
									<span><?php echo esc_html($count->students_discontinued); ?></span>
								</li>
							</ul>
						</div>
						<?php
					} ?>
				</div>
				<div class="row">
					<?php
					if ( current_user_can( 'wl_im_manage_enquiries' ) ) { ?>
						<div class="col bg-primary m-3 border-bottom border-primary blue-gradient">
							<div class="h5 mt-3 mb-3 text-white text-center "><i class="fas fa-envelope"></i> 
								<a href="<?php menu_page_url( 'institute-management-enquiries' ); ?>" class="text-white">
									<?php esc_html_e( 'Recent Enquiries', WL_IM_DOMAIN ); ?>
								</a>
							</div>
							<?php
							if ( count ( $recent_enquiries ) > 0 ) { ?>
								<ul class="list-group list-group-flush">
									<?php foreach( $recent_enquiries as $enquiry ) {
										$course = '-';
										if ( $enquiry->course_id && isset( $course_data[$enquiry->course_id] ) ) {
											$course_name = $course_data[$enquiry->course_id]->course_name;
											$course_code = $course_data[$enquiry->course_id]->course_code;
											$course      = "$course_name ($course_code)";
										}
										?>
										<li class="list-group-item align-items-center">
											<?php echo "<strong class='text-secondary'>" . WL_IM_Helper::get_enquiry_id( $enquiry->id ) . "</strong> - <strong>" . $course . "</strong>"; ?>
											<span class="text-secondary float-right">
												<?php echo date_format( date_create( $enquiry->created_at ), "d-m-Y g:i A" ); ?>
											</span>
										</li>
										<?php
									} ?>
								</ul>
								<?php
							} else { ?>
								<div class="text-white text-center pb-3"><?php esc_html_e( 'There is no enquiry.', WL_IM_DOMAIN ); ?></div>
								<?php
							} ?>
						</div>
						<?php
					}
					if ( current_user_can( 'wl_im_manage_courses' ) ) { ?>
						<div class="col bg-primary m-3 border-bottom border-primary blue-gradient">
							<div class="h5 mt-3 mb-3 text-white text-center"><i class="fas fa-graduation-cap"></i> 
								<a href="<?php menu_page_url( 'institute-management-courses' ); ?>" class="text-white">
									<?php esc_html_e( 'Popular Courses', WL_IM_DOMAIN ); ?>
								</a>
							</div>
							<?php
							$popular_courses_count = 0;
							if ( count ( $popular_courses_enquiries ) > 0 ) { ?>
								<ul class="list-group list-group-flush">
									<?php
									foreach( $popular_courses_enquiries as $enquiry ) {
										if ( $enquiry->course_id && isset( $course_data[$enquiry->course_id] ) ) {
											if ( $course_data[$enquiry->course_id]->is_deleted == 0 ) {
												$course_name = $course_data[$enquiry->course_id]->course_name;
												$course_code = $course_data[$enquiry->course_id]->course_code;
												$course      = "$course_name ($course_code)";
												?>
												<li class="list-group-item align-items-center">
													<?php echo "<strong>" . $course . "</strong>"; ?>
													<span class="text-secondary float-right">
														<?php echo esc_html($enquiry->students); ?> <?php echo ( $enquiry->students == 1 ) ? esc_html__( 'Student', WL_IM_DOMAIN ) : esc_html__( 'Students', WL_IM_DOMAIN ); ?>
													</span>
												</li>
												<?php
												$popular_courses_count++;
											}
										}
										?>
										<?php
									} ?>
								</ul>
								<?php
							} if ( $popular_courses_count == 0 ) { ?>
								<div class="text-white text-center pb-3"><?php esc_html_e( 'There is no popular course.', WL_IM_DOMAIN ); ?></div>
								<?php
							} ?>
						</div>
						<?php
					} ?>
				</div>
				<!-- end - card body content -->
			</div>
		</div>
	</div>
	<!-- end - row 2 -->
</div>