<h3>
	Statistiques des 7 derniers jours
</h3>

<table id="datatable">
	<thead>
		<tr>
			<th></th>
			<th>Nombre de visites</th>
			<th>Nombre de clics</th>
			<th>Taux de clics (%)</th>
		</tr>
	</thead>
	<tbody>
	<?php 
		foreach( $stats as $stat ) :
	?>
		<tr>
			<th><?= $stat['date'] ?></th>
			<td><?php
					$visit =  isset($stat['nbVisit']) ? $stat['nbVisit'] : 0;
					echo $visit;
				?>
			</td>
			<td><?php 
					$click =  isset($stat['nbClick']) ? $stat['nbClick'] : 0;
					echo $click;
				?>
			</td>
			<td>
				<?php 
					if ( $click !== 0 && $visit !== 0)
						$taux = round( ($click / $visit) * 100, 2);
					else
						$taux = 0;
					echo $taux;
				?>
			</td>
		</tr>
	<?php
		endforeach;
	?>
	</tbody>
</table>
<div id="container"></div>