<?php 
	if ( isset($stats) ) :
?>
	<table>
		<thead>
			<tr>
				<th colspan="7">
					<h3>
						Statistiques pour la journée d'hier.
					</h3>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td>
					Nombre de visiteur
				</td>
				<td>
					<?= $stats['nbVisit']; ?>
				</td>
				<td>
					Nombre de clique
				</td>
				<td>
					<?= $stats['nbClick']; ?>
				</td>
			</tr>
		</tbody>
	</table>
<?php 
	else :
?>
	<p>
		Aucune statistique n'est disponible pour la journée d'hier.
	</p>
<?php
	endif;
?>
