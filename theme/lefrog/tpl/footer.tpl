			{if $sessionOk}
				<p class="smaller">
					<i><br />{$lngConnectedAs}
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
				Powered by <a target="_blank" href="https://github.com/kissifrot/BestWishes">BestWishes</a> {$version} - <a href="{$webDir}/admin/">Admin</a>
			</div>
		</div>
	</body>
</html>