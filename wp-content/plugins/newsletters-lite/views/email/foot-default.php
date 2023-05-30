												<?php if ($this -> get_option('tracking') == "Y") : ?>
													<?php echo isset($eunique) ? esc_url_raw($this -> gen_tracking_link($eunique)) : ''; ?>
												<?php endif; ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<div class="footer">
							<table width="100%">
								<tr>
									<td class="aligncenter content-block">
										<a href="[newsletters_siteurl]">[newsletters_blogname]</a> | [newsletters_manage]
									</td>
								</tr>
							</table>
						</div>
					</div>
				</td>
				<td></td>
			</tr>
		</table>
	</body>
</html>