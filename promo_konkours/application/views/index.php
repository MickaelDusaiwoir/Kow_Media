<h2>
	Tous nos concours !!
</h2>

<?php

	foreach ($contests as $contest ) : ?>

	<article>

		<h2>
			<?= $contest->title ?>
		</h2>

		<?php 
			for ( $i=0; $i < $prizes.lenght ; $i++ ) :
				if ( $prizes[$i]['id'] == $contest->id ) :
		 ?>
			<p>
				<?= $prizes[$i]['title'] ?>
			</p>

			<img src="<?= $prizes[$i]['screenShot_url'] ?>" title="<?= $prizes[$i]['title'] ?>" alt="<?= $prizes[$i]['title'] ?>" />
			
			<p>
				Valeur du cadeau&nbsp;: <?= $prizes[$i]['value'] ?>
			</p>
		<?php 
				endif;
			endfor; 
		?>

		<p>
			<?= $contest->text ?>
		</p>

		<?= anchor($contest->url, 'Cliquez ICI pour jouer à ce concours', array('title' => 'Cliquez ICI pour jouer à ce concours')); ?>

	</article>
		
	<?php endforeach;

	echo anchor('admin/addContest', 'Ajouter un concours', array('title' => 'Ajouter un concours')); 

?>
