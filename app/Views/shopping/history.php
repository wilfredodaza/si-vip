<?= $this->extend('layouts/main') ?>


<?= $this->section('title') ?> Facturación <?= $this->endSection() ?>
<?= $this->section('styles') ?>

<link rel="stylesheet" href="<?= base_url('css/app-email-content.css') ?>">
<link rel="stylesheet" href="<?= base_url('css/page-timeline.css') ?>">

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/sweetalert/sweetalert.css">

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/jquery.dataTables.min.css">
	
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/css/responsive.dataTables.min.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/data-tables/css/select.dataTables.min.css">

<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/css/pages/data-tables.css">
<link rel="stylesheet" type="text/css" href="<?= base_url() ?>/app-assets/vendors/dropify/css/dropify.min.css">
<style>
		.timeline:before {
				left: 100% !important;
		}
</style>
<?= $this->endSection() ?>
<?= $this->section('content') ?>
<div id="main">
		<div class="row">
				<div class="col s12">
						<?php if (session('success')): ?>
								<div class="card-alert card green lighten-5">
										<div class="card-content green-text">
												<?= session('success') ?>
										</div>
										<button type="button" class="close green-text" data-dismiss="alert"
														aria-label="Close">
												<span aria-hidden="true">×</span>
										</button>
								</div>
						<?php endif; ?>
						<?php if (session('error')): ?>
								<div class="card-alert card red lighten-5">
										<div class="card-content red-text">
												<?= session('error') ?>
										</div>
										<button type="button" class="close red-text" data-dismiss="alert"
														aria-label="Close">
												<span aria-hidden="true">×</span>
										</button>
								</div>
						<?php endif; ?>
				</div>
				<div class="col s12 ">
						<!-- Content Area Starts -->
						<div class="app-email-content">
								<div class="content-area col s12 m8">
										<div class="app-wrapper">
												<div class="card card-default scrollspy border-radius-6 fixed-width">
														<div class="card-content pt-0">
																<div class="row">
																		<div class="col s12">
																				<!-- Email Header -->
																				<div class="email-header">
																						<div class="subject">
																								<div class="back-to-mails">
																										<a href="<?= base_url([$module->name]) ?>"><i class="material-icons">arrow_back</i></a>
																								</div>
																								<div class="email-title"><?php
																									$string = explode(';', $mail->subject);
																									echo implode('; ', $string);
																								?></div>
																						</div>
																						
																				</div>
																				<!-- Email Header Ends -->
																				<hr>
																				<!-- Email Content -->
																				<div class="email-content">
																						<div class="list-title-area">
																								<div class="user-media">
																										<img src="../../../app-assets/images/user/9.jpg" alt="" class="circle z-depth-2 responsive-img avtar">
																										<div class="list-title">
																												<span class="name"><?= !empty($mail->customer_name) ?  $mail->customer_name : $mail->name ?></span>
																												<span class="to-person"><?= !empty($mail->customer_identification) ?  $mail->customer_identification : 'No registrado' ?></span>
																										</div>
																								</div>
																								<div class="title-right">
																										<span class="mail-time"><?= date_fecha($mail->created_at) ?> (<?= different( $mail->created_at) ?>)</span>
																								</div>
																						</div>
																						<div class="email-desc">

																						<?= $mail->body ?>
																										
																								
																								
																						</div>
																				</div>
																				<!-- Email Content Ends -->
																				<hr>
																				<!-- Email Footer -->
																				<div class="email-footer">
																						<h6 class="footer-title">Archivos Adjuntos (<?= count($files) ?>)</h6>
																						<div class="footer-action">
																								<div class="attachment-list">
																									<?php foreach ($files as $key => $file): ?>
                                                      <div class="attachment">
                                                          <img src="<?= base_url('images/folder.png') ?>" alt="" class="responsive-img attached-image">  
                                                          <div class="size">
                                                              <span class="grey-text truncate tooltipped" data-position="bottom" data-tooltip="<?= $file->name ?>" style="width:80px"><?= $file->name ?></span>
                                                          </div>
                                                          <div class="links">
                                                              <a href="<?= base_url(['shopping', 'download', $file->name, 1]) ?>" target="_blank" class="Right">
                                                                  <i class="material-icons">file_download</i>
                                                              </a>
                                                          </div>
                                                      </div>
																									<?php endforeach ?>
																								</div>
																						</div>
																						<div class="reply-box d-none">
																								<form action="#">
																										<div class="input-field col s12">
																												<div class="snow-container mt-2">
																														<div class="compose-editor"></div>
																														<div class="compose-quill-toolbar">
																																<span class="ql-formats mr-0">
																																		<button class="ql-bold"></button>
																																		<button class="ql-italic"></button>
																																		<button class="ql-underline"></button>
																																		<button class="ql-link"></button>
																																		<button class="ql-image"></button>
																																</span>
																														</div>
																												</div>
																										</div>
																										<div class="input-field col s12">
																												<a class="btn reply-btn right">Reply</a>
																										</div>
																								</form>
																						</div>
																						<div class="forward-box d-none">
																								<hr>
																								<form action="#">
																										<div class="input-field col s12">
																												<i class="material-icons prefix"> person_outline </i>
																												<input id="email" type="email" class="validate">
																												<label for="email">To</label>
																										</div>
																										<div class="input-field col s12">
																												<i class="material-icons prefix"> title </i>
																												<input id="subject" type="text" class="validate">
																												<label for="subject">Subject</label>
																										</div>
																										<div class="input-field col s12">
																												<div class="snow-container mt-2">
																														<div class="forward-email"></div>
																														<div class="forward-email-toolbar">
																																<span class="ql-formats mr-0">
																																		<button class="ql-bold"></button>
																																		<button class="ql-italic"></button>
																																		<button class="ql-underline"></button>
																																		<button class="ql-link"></button>
																																		<button class="ql-image"></button>
																																</span>
																														</div>
																												</div>
																										</div>
																										<div class="input-field col s12">
																												<a class="btn forward-btn right">Forward</a>
																										</div>
																								</form>
																						</div>
																				</div>
																				<!-- Email Footer Ends -->
																		</div>
																</div>
														</div>
												</div>
										</div>
								</div>
								<div class="col s12 l4">
										<?php if($mail->type_documents_id_invoices != 112 && $mail->type_documents_id_invoices != 113): ?>
											<div class="card">
													<div class="card-content">
														<H5> Documentos Requeridos  <a href="#modal1" class="btn btn-small right  modal-trigger" style="padding-left: 10px; padding-right:10px;">
																	<i class="material-icons">file_upload</i>
															</a> </H5>
														
														<div class="divider"></div>
														<br>
															<form action="#">
																	<p>
																	<label>
																			<input type="checkbox" checked="checked" />
																			<span>Factura Electronica</span>
																	</label>
																	</p>
																	<?php foreach($types as $type): ?>
																		<p>
																			<span style="display:flex">
																				<?php
																					$validation = null;
																					foreach ($invoices_file as $key => $file) {
																						if($file->invoices_type_files_id == $type->id){
																							$validation = $file;
																							break;
																						}
																					}
																					if(!empty($validation)){
																						if($validation->status == 'Aceptado')
																							$icon = '<i class="material-icons text-green green-text">check</i> &nbsp;';
																						if($validation->status == 'Rechazado')
																							$icon = '<i class="material-icons text-green orange-text">error_outline</i> &nbsp;';
																						if($validation->status == 'Pendiente')
																							$icon = '<i class="material-icons text-green orange-text">priority_high</i> &nbsp;';
																					}else
																						$icon = '<i class="material-icons text-green red-text">close</i> &nbsp;';
																				?>
																				<?= $icon. $type->name ?>
																				<?php if(!empty($validation)): ?>
																					<?php if(!empty($validation->name)): ?>
																						<a href="<?= base_url(['invoices_files',$company->identification_number, $file->name]) ?>" target="_blank" class="Right">
																								<i class="material-icons">file_download</i>
																						</a>
																					<?php endif ?>
																				<?php endif ?>
																			</span>
																		</p>
																	<?php endforeach ?>
															</form>
													</div>
											</div>
										<?php endif ?>
											<div class="card">
													<div class="card-content">
														<H5> Datos de factura</H5>
														<div class="divider"></div>
														<?php
																if(!empty($mail->payment_due_date)){
																	$fecha_now = new DateTime(date('Y-m-d H:i:s'));
																	$fecha_payment = new DateTime($mail->payment_due_date.' 23:59:59');
																	$diff = $fecha_now->diff($fecha_payment);
																	if($diff->days >= 1 && $diff->invert == 0)
																			$mail->vence = $diff->format('%d').'D <i class="material-icons text-green green-text tiny">brightness_1</i>';
																	elseif($diff->days == 0 && $diff->invert == 0){
																			if($diff->format('%H') >= 12)
																					$mail->vence = $diff->format('%H').'H <i class="material-icons text-green green-text tiny">brightness_1</i>';
																			elseif($diff->format('%H') <= 11 && $diff->format('%H') >= 4)
																					$mail->vence = $diff->format('%H').'H <i class="material-icons text-yellow yellow-text tiny">brightness_1</i>';
																			else
																					if($diff->format('%H') > 0)
																							$mail->vence = $diff->format('%H').'H <i class="material-icons text-red red-text tiny">brightness_1</i>';
																					else
																							$mail->vence = $diff->format('%i').'M <i class="material-icons text-red red-text tiny">brightness_1</i>';
																	} else  $mail->vence = $diff->days >= 1 ? $diff->format('%d').'D <i class="material-icons text-red red-text tiny">brightness_1</i>' : $diff->format('%h').'H <i class="material-icons text-red red-text tiny">brightness_1</i>';                ;
																}else $mail->vence = 'No aplica';
															?>
														<table>
															<tbody>
																<tr>
																	<td><b>#</b></td>
																	<td><?= $mail->resolution ?></td>
																</tr>
																<tr>
																	<td><b>Proveedor</b></td>
																	<td><?= $mail->company_name ?></td>
																</tr>
																<tr>
																	<td><b>Cliente</b></td>
																	<td><?= $mail->customer_name ?></td>
																</tr>
																<tr>
																	<td><b>Fecha</b></td>
																	<td><?= $mail->created_at ?></td>
																</tr>
																<tr>
																	<td><b>Vence</b></td>
																	<td><?= $mail->vence ?></td>
																</tr>
																<tr>
																	<td><b>Valor</b></td>
																	<td>$<?= number_format($mail->valor, 0, ',', '.') ?></td>
																</tr>
															</tbody>
														</table>
													</div>
											</div>
									</div>
						</div>
				</div>

		 
				<div class="breadcrumbs-inline pt-3 pb-1" id="breadcrumbs-wrapper">
						<div class="container">
								<div class="row">
										<div class="col s10 m6 l6 breadcrumbs-left">
												<h5 class="breadcrumbs-title mt-0 mb-0 display-inline hide-on-small-and-down ">
														<span>
															 Historia
														</span>
												</h5>
										</div>
								</div>
						</div>
				</div>

				<div class="col-12 mb-5">
						<div class="container">
								<div class="section">
										<div class="card">
												<div class="card-content section-data-tables">
														<table class="display">
																<thead>
																		<tr>
																				<th>Fecha</th>
																				<th>Usuario</th>
																				<th>Observación</th>
																		</tr>
																</thead>
																<tbody>
																	<?php foreach($historys as $history): ?>
																		<tr>
																				<td><?= $history->created_at ?></td>
																				<td><?= $history->name ?></td>
																				<td><?= $history->observation ?></td>
																		</tr>
																	<?php endforeach ?>
																</tbody>
														</table>
												</div>
										</div>
								</div>
						</div>
				</div>
		</div>
</div>

<div id="modal1" class="modal modal-fixed-footer">
		<div class="modal-content">
				<h6>Cargar Archivos</h6>
				<br>
				<form action="<?= base_url(['shopping', 'file']) ?>" method="POST" enctype="multipart/form-data" id="form-file">
					<input type="hidden" name="invoices_id" value="<?= $mail->invoices_id ?>">
					<input type="hidden" name="exist" id="exist" value="false">
					<input type="hidden" name="shopping_id" value="<?= $mail->id ?>">
          <input type="hidden" name="url" value="<?= base_url(['shopping', 'history', $mail->id, $module->id]) ?>">
					<div class="row">
						<div class="input-field col s12">
							<select id="select-archivo" onchange="getId(this);" name="type">
								<option value="" disabled selected>Seleccione el tipo de archivo</option>
								<?php foreach($types as $type): ?>
									<option value="<?= $type->id ?>"><?= $type->name ?></option>
								<?php endforeach ?>
							</select>
							<label>Tipo de archivo</label>
						</div>
						<div class="input-field col s12">
								<input id="numero" type="text" name="numero" class="validate">
								<label for="numero">Número</label>
						</div>
						<div class="input-field col s12">
							<textarea id="observation" name="observation" class="materialize-textarea"></textarea>
							<label for="observation">Observación</label>
						</div>
						<div class="row section">
							<div class="col s12">
									<input type="file" id="input-file" name="file" class="dropify-Es"/>
							</div>
						</div>
					</div>
				</form>
		</div>
		<div class="modal-footer">
				<a href="#!" class="modal-action modal-close waves-effect waves-purple btn-flat ">Cerrar</a>
				<a href="#!" class="modal-action btn purple waves-effect" onclick="guardar()">Guardar</a>
		</div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
		<script src="<?= base_url('/js/advance-ui-modals.js') ?>"></script>
		<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/jquery.dataTables.min.js"></script>
		<script src="<?= base_url() ?>/app-assets/vendors/data-tables/extensions/responsive/js/dataTables.responsive.min.js"></script>
		<script src="<?= base_url() ?>/app-assets/vendors/data-tables/js/dataTables.select.min.js"></script>
		<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="<?= base_url() ?>/assets/js/new_scripts/funciones.js"></script>
		<script src="<?= base_url() ?>/app-assets/vendors/dropify/js/dropify.min.js"></script>
		<script src="<?= base_url() ?>/app-assets/js/scripts/form-file-uploads.js"></script>
		<script>
				const table = [];
				$(document).ready(function(){
						table = $(`table.display`).DataTable({
								"responsive": false,
								"scrollX": true,
								"ordering": false,
								language: { url: "https://cdn.datatables.net/plug-ins/1.11.5/i18n/es-ES.json"}
						});
				});

				function aux_drop(){
					var drEvent = $('.dropify-Es').dropify({
							messages: {
									default: 'Arrastre y suelte un archivo aquí o haga clic.',
									replace: 'Arrastre y suelte un archivo o haga clic para reemplazar',
									remove: 'Borrar',
									error: 'Ooops, sucedió algo malo.'
							},
							error: {
									'fileSize': 'The file size is too big ({{ value }} max).',
									'minWidth': 'The image width is too small ({{ value }}}px min).',
									'maxWidth': 'The image width is too big ({{ value }}}px max).',
									'minHeight': 'The image height is too small ({{ value }}}px min).',
									'maxHeight': 'The image height is too big ({{ value }}px max).',
									'imageFormat': 'The image format is not allowed ({{ value }} only).',
									'fileExtension': 'El archivo no está permitido. Formato válido ({{ value }}).',
							}
					});
					return drEvent;
				}

				function getId(id){
					var files = <?= json_encode($invoices_file) ?>;
					var drEvent = aux_drop();
					drEvent = drEvent.data('dropify');
					drEvent.resetPreview();
					drEvent.clearElement();

					files.every((file, id_aux) => {
						if(id.value == file.invoices_type_files_id){
							console.log(`<?= base_url() . '/invoices_files/' ?>${file.name}`);
							drEvent.settings.defaultFile = `<?= base_url() . '/invoices_files/' ?>${file.name}`;
							drEvent.destroy();
							drEvent.init();
							$('#exist').val(true);
							return false;
						}
							drEvent.settings.defaultFile = "";
							drEvent.destroy();
							drEvent.init();
							$('#exist').val(false);
							return true;
					})
				}

				function guardar(){
					if ($(`#select-archivo`).val() === null) {
						return alert('<span class="red-text">Debe seleccionar un tipo de archivo</span>', 'red lighten-5');
					}
					// if ($(`#input-file`).val() === '' && $('#exist').val() === 'false') {
					// 	return alert('<span class="red-text">Debe seleccionar un archivo</span>', 'red lighten-5');
					// }
					if ($(`#numero`).val() === '') {
						return alert('<span class="red-text">Debe ingresar un número</span>', 'red lighten-5');
					}
					$('#form-file').submit();
				}
				
		</script>
<?= $this->endSection() ?>