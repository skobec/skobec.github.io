<?

class Breadcrumb {
	
	/**
	* put your comment there...
	* 
	* @param object[] $breadcrumb_list
	* @param string $current_text
	*/
	static function draw($breadcrumb_list, $current_text = null) {
		$cnt = count($breadcrumb_list);
		$arrow = '&raquo;';
		$arrow = '&#9658;';
		?>
    		<ol class="breadcrumb" itemscope itemtype="http://schema.org/BreadcrumbList">
    			<?foreach($breadcrumb_list as $i => $breadcrumb) {?>
				    <li itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
				        <a href="<?=$breadcrumb->url?>" itemprop="item"><span itemprop="name"><?=htmlspecialchars($breadcrumb->title)?></span></a>
				        <?if($i<$cnt-1){echo "<i>{$arrow}</i>";}?>
				    </li>
			    <?}?>
			    <?if($current_text){?>
			    	<i><?=$arrow?></i> <?=htmlspecialchars($current_text)?>
			    <?}?>
			    <?/*
			    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
			        <a href="/vacancy/" itemprop="url"><span itemprop="title">Все вакансии</span></a>
			        &raquo;
			    </li>
			    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
			        <a href="/vacancy/rubric/<?=$this->card->rubric_code?>/" itemprop="url"><span itemprop="title"><?=htmlspecialchars($this->card->rubric_title)?></span></a>
			        &raquo;
			    </li>  
			    <li itemscope itemtype="http://data-vocabulary.org/Breadcrumb">
			        <a href="/market/card/id/<?=$this->card->organization_id?>/?vacancy" itemprop="url"><span itemprop="title"><?=htmlspecialchars($this->card->brand_name)?></span></a>
			    </li>*/?>
			</ol>
		<?
	}

}