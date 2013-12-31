			{if $sessionOk}
				<p class="smaller">
					<i><br />{$lngConnectedAs}
					<a href="{$webDir}/logout.php" data-role="button" data-icon="back">{$lngLogout}</a> 
					</i>
				</p>
			{/if}
				</div>
			</div><!-- /content -->
			<div class="footer-docs ui-footer ui-bar-c" data-theme="b" data-role="footer" role="contentinfo">
				<p>Powered by BestWishes {$version}</p>
			</div>
		</div><!-- /page -->
	</body>
</html>