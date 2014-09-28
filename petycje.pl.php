<?php
	$ret = array();

	function parseItem( $content, $pos)
	{
		global $ret;

		$posClose = strpos( $content, '</table>', $pos + 1);
		$table = substr( $content, $pos, $posClose - $pos);
		$table = mb_convert_encoding( $table, "UTF-8", "iso-8859-2");
		$data = array();
		$pos = 0;

		$pos = strpos( $table, 'petycjeid=', $pos);
		$data[ id] = intval( substr( $table, $pos + 10, strpos( $table, "'", $pos) - $pos - 9));

		$pos = strpos( $table, '>', $pos);
		$data[ datetime] = trim( substr( $table, $pos + 1, strpos( $table, '<br>', $pos) - $pos - 1));

		$pos = strpos( $table, '<br>', $pos);
		$data[ datetime] .= ' ' .trim( substr( $table, $pos + 4, strpos( $table, '<', $pos + 1) - $pos - 4));

		$pos = strpos( $table, 'mainListNazwa', $pos);
		$pos = strpos( $table, '>', $pos);
		$data[ title] = trim( substr( $table, $pos + 1, strpos( $table, '<', $pos) - $pos - 1));

		$pos = strpos( $table, 'mailto:', $pos);
		$data[ mail] = substr( $table, $pos + 7, strpos( $table, "'", $pos + 1) - $pos - 7);

		$pos = strpos( $table, '>', $pos);
		$data[ author] = trim( substr( $table, $pos + 1, strpos( $table, '<', $pos + 1) - $pos - 1));

		$pos = strpos( $table, '<br>Do:', $pos);
		$data[ office] = trim( substr( $table, $pos + 7, strpos( $table, '<', $pos + 1) - $pos - 7));

		$pos = strpos( $table, 'mainList', $pos);
		$pos = strpos( $table, '>', $pos);
		$data[ signature] = intval( trim( substr( $table, $pos + 1, strpos( $table, '<br>', $pos + 1) - $pos - 1)));

		$pos = strpos( $table, '<br>', $pos);
		$data[ verified] = intval( trim( substr( $table, $pos + 4, strpos( $table, '<', $pos + 4) - $pos - 1), ' ()'));

		$ret[ data][] = $data;
	}

	function parsePage( $paramTitles, $paramBodys, $paramSearchtext, $paramCategoria, $paramRegion, $paramPage)
	{
		global $ret;

		$dataURL = 'http://petycje.pl/petycjeSearch.php?titles=' . $paramTitles. '&bodys=' . $paramBodys . '&searchtext=' . $paramSearchtext . '&kategoria=' . $paramCategoria . '&region=' . $paramRegion . '&page=' . $paramPage;
		$ret[ url] = $dataURL;

		$content = file_get_contents( $dataURL);
		$posTable = strpos( $content, '<body');

		for( $i = 0; $i < 12; ++$i) {
			$posTable = strpos( $content, '<table ', $posTable + 1);
		}

		$posNext = strpos( $content, '<table ', $posTable + 1);
		$posClose = strpos( $content, '</table>', $posTable + 1);

		// Ignore the first item. It's just a headline.
		$posTable = $posClose;
		$posNext = strpos( $content, '<table ', $posTable + 1);
		$posClose = strpos( $content, '</table>', $posTable + 1);

		while( $posNext < $posClose) {
			parseItem( $content, $posNext);
			$posTable = $posClose;

			$posNext = strpos( $content, '<table ', $posTable + 1);
			$posClose = strpos( $content, '</table>', $posTable + 1);
		}

		if( 1 == $paramPage) {
			$posLink = strpos( $content, '<a href="petycjeSearch.php?', $posClose);

			while( false !== $posLink) {
				$posLink = strpos( $content, '&page=', $posLink);
				$page = substr( $content, $posLink + 6, strpos( $content, '&', $posLink + 1) - $posLink - 6);

				parsePage( $paramTitles, $paramBodys, $paramSearchtext, $paramCategoria, $paramRegion, intval( $page));

				$posLink = strpos( $content, '<a href="petycjeSearch.php?', $posLink + 1);
			}
		}
	}

	$paramTitles = 'on';
	$paramBodys = 'on';
	$paramSearchtext = '';
	$paramCategoria = 0;
	$paramRegion = 0;

	if( !isset( $_GET[ 'region'])) {
		$ret[ error] = "Parameter 'region' missing.";
		echo json_encode( $ret);
		exit;
	}

	if( 'lodzkie' == $_GET[ 'region']) {
		$paramRegion = 52; // Łódzkie
	} else {
		$ret[ error] = "Parameter 'region' must be 'lodzkie'.";
		echo json_encode( $ret);
		exit;
	}

	parsePage( $paramTitles, $paramBodys, $paramSearchtext, $paramCategoria, $paramRegion, 1);

/*				$obj = simplexml_load_string( $content);

				if( 'WFS' == $type) {
					$ret[ title] = trim( $obj->ows_ServiceIdentification->ows_Title);
					$ret[ fees] = trim( $obj->ows_ServiceIdentification->ows_Fees);
					$ret[ provider] = trim( $obj->ows_ServiceProvider->ows_ProviderName);
					$ret[ wfs] = trim( $obj->wfs_FeatureTypeList->wfs_FeatureType->wfs_Name);
				} else if( 'WMS' == $type) {
					$ret[ title] = trim( $obj->Service->Title);
					$ret[ fees] = trim( $obj->Service->Fees);
				}

//				$ows_Value = '';
//				foreach( $obj->ows_OperationsMetadata->ows_Parameter->ows_Value as $value) {
//					$ows_Value .= $value . ',';
//				}
//				$ret[ ows] = trim( $ows_Value, ',');

				// get feature
				if( 'WFS' == $type) {
					$addons = '?SERVICE=' . $type . '&VERSION=1.1.0&REQUEST=DescribeFeatureType&TYPENAME=' . $ret[ wfs];

					$addons = '?SERVICE=' . $type . '&VERSION=1.1.0&REQUEST=GetFeature&TYPENAME=' . $ret[ wfs];
				} else if( 'WMS' == $type) {
					$addons = '?SERVICE=' . $type . '&VERSION=1.1.0&REQUEST=GetCapabilities';
					$content = file_get_contents( $dataURL . $addons);
					$obj = simplexml_load_string( $content);

					$ret[ layer] = $obj->Capability->Layer->Title;

					$addons = '?SERVICE=' . $type . '&VERSION=1.1.0&REQUEST=GetFeatureInfo&query_layers=' . urlencode( $ret[ layer] $ret[ title]) . '&x=0&y=0';
				}

				$content = file_get_contents( $dataURL . $addons);
				$content = str_replace( 'gml:', 'gml_', $content);
				$content = str_replace( 'fis:', 'fis_', $content);
				$content = str_replace( 'wfs:', 'wfs_', $content);

				if( strlen( $content) > 8000000) {
					$ret[ error] = "Too many data. Unable to parse.";
				} else {
					try {
						$obj = simplexml_load_string( $content, 'SimpleXMLElement', LIBXML_COMPACT | LIBXML_PARSEHUGE);

						$ret[ obj] = $obj;
					} catch (Exception $e) {
						$ret[ error] = 'XML error: ' . $e->getMessage();
					}
				}*/

	echo json_encode( $ret);
?>
