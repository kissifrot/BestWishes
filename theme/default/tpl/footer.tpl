			{if $sessionOk}
				<p class="copyright">
					<i><br />Connected as <b>{$user->name}</b>
					<br /><a href="{$webDir}/logout.php"><img src="{$themeWebDir}/img/logout.png" alt="" class="icon_text" /> {$lngLogout}</a>
					<br /><a href="{$webDir}/options.php"><img src="{$themeWebDir}/img/options.png" alt="" class="icon_text" /> {$lngChangeOptions}</a>
					</i>
				</p>
			{/if}
				</div>
			</div>
			<br /><br /><br />
			<br style="clear: both" />
			<div id="footer">
				Powered by BestWishes {$version} - <a href="{$webDir}/admin/">Admin</a>
			</div>
		</div>
	</body>
</html>