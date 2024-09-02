const { __, _x, _n, sprintf } = wp.i18n;

jQuery( function( $ ) {
	var referer = $( 'input[name="_wp_http_referer"]' ).val();

	// 行タイトル 接頭語／接尾語 選択項目 - 変更時イベント
	$( 'textarea#row_title_prefix_items,textarea#row_title_suffix_items' ).on( 'change', function() {
		var id = $( this ).attr( 'id' );
		var selectControl = null;
		if ( id == 'row_title_prefix_items' ) {
			selectControl = $( 'select#default_row_title_prefix' );
		} else if ( id == 'row_title_suffix_items' ) {
			selectControl = $( 'select#default_row_title_suffix' );
		} else {
			return;
		}
		var selectedValue = selectControl.children( 'option:selected' ).val();
		selectControl.children( 'option' ).remove();
		selectControl.append( $( '<option>', {
			value: '',
			text: __( '(表示しない)', 'changelog-extend' ),
		} ) );
		$( this ).val().split( "\n" ).forEach( ( value ) => {
			if ( value.length == 0 ) {
				return;
			}
			selectControl.append( $( '<option>', {
				value: value,
				text: value,
			} ) );
		} );
		selectControl.children( 'option' ).each( ( index, element ) => {
			if ( $( element ).val() == selectedValue ) {
				$( element ).prop( 'selected', true );
			}
		} );
	} );

	// 初期値に戻すボタン - クリック時イベント
	$( '#init' ).on( 'click', function() {
		if ( ! confirm( __( '全ての値を初期値に戻してもよろしいですか？', 'changelog-extend' ) ) ) {
			return;
		}

		$.ajax( {
			type: 'post',
			url: ml_ec_js_data.ajax_url,
			data: {
				action: ml_ec_js_data.action,
				nonce: ml_ec_js_data.nonce,
				_wp_http_referer: referer,
				command: $( this ).data( 'command' ),
			},
		} ).done( function( data, testStatus, xhr ) {
			location.reload();
		} ).fail( function( xhr, testStatus, e ) {
			alert( 'エラーが発生しました。\nエラー : ' + xhr.responseText );
		} );
	} );

	// 設定フォーム - 送信時イベント
	$( '#setting_form' ).submit( function() {
		return confirm( __( '保存してもよろしいですか？', 'changelog-extend' ) );
	} );
} );
