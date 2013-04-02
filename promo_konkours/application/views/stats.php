<h3>
	Statistiques des 7 derniers jours
</h3>

<table id="datatable">
	<thead>
		<tr>
			<th></th>
			<th>Visites</th>
			<th>Clics</th>
			<th>Clics uniques</th>
			<th>Taux de clics uniques (%)</th>
			<th>Concours / visiteur</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		foreach( $stats as $stat ) :
	?>
		<tr>
			<th><?= $stat['date'] ?></th>
			<td><?php // nombre de visite
					$visit =  isset($stat['nbVisit']) ? $stat['nbVisit'] : 0;
					echo $visit;
				?>
			</td>
			<td><?php // nombre de clic
					$click =  isset($stat['nbClick']) ? $stat['nbClick'] : 0;
					echo $click;
				?>
			</td>
			<td>
				<?php 
					$clickUnique = isset($stat['clickUnique']) ? $stat['clickUnique'] : 0;
					echo $clickUnique;
				?>
			</td>
			<td>
				<?php  // Taux de clics uniques, on arrobi a 3 chiffre aprés la virgule.
					if ( $clickUnique !== 0 && $visit !== 0)
						$taux = round( ($clickUnique / $visit) * 100, 3);
					else
						$taux = 0;
					echo $taux;
				?>
			</td>
			<td> 
				<?php // concours / visiteurs
					$avgClick = isset($stat['avgClick']) ? $stat['avgClick'] : 0 ;
					echo round($avgClick, 3);
				?>
			</td>
		</tr>
	<?php
		endforeach;
	?>
	</tbody>
</table>
<div id="container"></div>