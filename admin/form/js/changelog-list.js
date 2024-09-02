const { __, _x, _n, sprintf } = wp.i18n;

jQuery( function( $ ) {
	var referer = $( 'input[name="_wp_http_referer"]' ).val();
	var controls = function() {
		var result = [];
		var formControls = 	$( '#edit_form' ).find( 'input,select,textarea' );
		formControls.each( function() {
			var control = $( this );
			var type = control.attr( 'type' );
			var tagName = control.prop( 'tagName' ).toLowerCase();
			var name = control.attr( 'name' );
			var value;
			if ( tagName == 'select' ) {
				value = [];
				var options = control.find( 'option' );
				options.each( function() {
					if ( $( this ).prop( 'checked' ) || $( this ).prop( 'selected' ) ) {
						value.push( $( this ).val() );
					}
				}) ;
			 } else if ( type == 'checkbox' ) {
				 value = control.prop( 'checked' );
			 } else if ( type == 'radio' ) {
				if ( control.prop( 'checked' ) ) {
					 value = control.val();
				} else {
					 value = null;
				}
			} else {
				 value = control.val();
			}
			result.push( {
				name: name,
				value: value
			} );
		});
		return result;
	}();

	// 非表示チェックボックス - クリックイベント
	$( 'input#hidden_cb' ).on( 'click', function() {
		var control = $( this );

		// 通信中操作できないように一時的に無効にする
		disableListItem( true );

		// 非表示状態を更新する
		var checkFlag = control.prop( 'checked' );
		$.ajax( {
			type: 'post',
			url: ml_ec_js_data.ajax_url,
			data: {
				action: ml_ec_js_data.action,
				nonce: ml_ec_js_data.nonce,
				_wp_http_referer: referer,
				command: control.data( 'command' ),
				id: control.data( 'post-id' ),
				check: checkFlag,
			},
		} ).done( function( data, testStatus, xhr ) {
			// 処理なし
		} ).fail( function( xhr, testStatus, e ) {
			// エラーになった場合は元に戻す
			control.prop( 'checked', !checkFlag )

			// エラーメッセージを表示する
			alert( xhr.responseText );
		} ).always( function() {
			// 無効にした項目を有効にする
			disableListItem( false);
		} );
	} );

	// タイトル接頭語／接尾語切替ボタン - クリックイベント
	$( '#title_prefix_change,#title_suffix_change,#prefix_change,#suffix_change' ).on( 'click', function() {
		$( this ).siblings( 'select,input' ).toggle();
		if ( $( this ).siblings( 'select' ).is( ':visible' ) ) {
			$( this ).removeClass( 'dashicons-list-view' );
			$( this ).addClass( 'dashicons-edit' );
		} else {
			$( this ).removeClass( 'dashicons-edit' );
			$( this ).addClass( 'dashicons-list-view' );
		}
	});
	// タイトル接頭語／接尾語コントロール - 変更時イベント
	$( 'select#title_prefix,input#title_prefix,select#title_suffix,input#title_suffix,select#prefix,input#prefix,select#suffix,input#suffix' ).on( 'change', function() {
		var control = $( this );

		// リスト内のアイテムの場合は通信中操作できないように一時的に無効にする
		var id = control.attr( 'id' );
		if ( id == 'title_prefix' || id == 'title_suffix' ) {
			disableListItem( true );
		}

		var tagName = control.prop( 'tagName' ).toLowerCase();
		var value = null;
		if ( tagName == 'select' ) {
			// 選択した値をテキストフィールドに設定する
			value = control.children( 'option:selected' ).val();
			control.siblings( 'input' ).val( value );
		} else if ( tagName == 'input' ) {
			// セレクトボックスに入力した値と同じ選択肢がある場合は選択する
			value = control.val();
			selectComboBoxItem( control.siblings( 'select' ), value );
		} else {
			return;
		}

		// ダイアログ内のアイテムの場合はここで戻る
		if ( id == 'prefix' || id == 'suffix' ) {
			return;
		}

		// リスト内のアイテムの場合はタイトル接頭語／接尾語を更新する
		$.ajax( {
			type: 'post',
			url: ml_ec_js_data.ajax_url,
			data: {
				action: ml_ec_js_data.action,
				nonce: ml_ec_js_data.nonce,
				_wp_http_referer: referer,
				command: control.data( 'command' ),
				id: control.data( 'post-id' ),
				value: value,
			},
		} ).done( function( data, testStatus, xhr ) {
			// 処理なし
		} ).fail( function( xhr, testStatus, e ) {
			// エラーメッセージを表示する
			alert( xhr.responseText );
		} ).always( function() {
			// 無効にした項目を有効にする
			disableListItem( false );
		} );
	} );

	// 編集ボタン - クリックイベント
	$( 'button#edit' ).on( 'click', function() {
		// 取得元キーを退避しておく
		$( '[name=post_id]').val( $( this ).val() );

		// ダイアログを表示する
		$( '#edit_dialog' ).dialog( {
			title: __( '編集', 'changelog-extend' ),
			modal: true,
			minWidth: 600,
			minHeight: 250,
			maxHeight: 600,
		} );

		// 入力項目をクリアする
		clearForm();

		// 読み込みが終わるまで無効化する
		disableFormItem( true );

		// 編集データを取得する
		$.ajax( {
			type: 'post',
			url: ml_ec_js_data.ajax_url,
			data: {
				action: ml_ec_js_data.action,
				nonce: ml_ec_js_data.nonce,
				_wp_http_referer: referer,
				command: $( this ).data( 'command' ),
				id: $( this ).data( 'post-id' ),
			},
		} ).done( function( data, testStatus, xhr ) {
			// 受信したデータをセットする
			$.each( data, function( name, value ) {
				var selector = $( '[name=' + name + ']' );
				if ( selector.length == 0 ) {
					return;
				}
				var tagName = selector.prop( 'tagName' ).toLowerCase();
				if ( tagName == 'input' && selector.attr( 'type' ) == 'checkbox' ) {
					selector.prop( 'checked', value == 1 );
				} else {
					selector.val( value );
				}
			} );
			if ( ! selectComboBoxItem( $( 'select#prefix' ), $( 'input#prefix' ).val() ) ) {
				$( 'select#prefix,input#prefix' ).toggle();
			}
			if ( ! selectComboBoxItem( $( 'select#suffix' ), $( 'input#suffix' ).val() ) ) {
				$( 'select#suffix,input#suffix' ).toggle();
			}

			// 無効化を解除する
			disableFormItem( false );

			// 投稿種別が更新履歴以外の場合はコントロールを読み取り専用にする
			var readonly = data[ 'type' ] != 'changelog';
			$( '[name=date_time]' ).attr( 'readonly', readonly );
			$( '[name=title]' ).attr( 'readonly', readonly );
		} ).fail( function( xhr, testStatus, e ) {
			console.log( 'ajax失敗 : ' + status );
			console.log( XMLHttpRequest );
			closeDialog();
		} );
	} );

	// キャンセルボタン - クリックイベント
	$( 'button#cancel' ).on( 'click', closeDialog );

	// 新規追加ボタン - クリックイベント
	$( 'button#addnew' ).on( 'click', function() {
		// ダイアログを表示する
		$( '#edit_dialog' ).dialog( {
			title: __( '新規追加', 'changelog-extend' ),
			modal: true,
			minWidth: 600,
			minHeight: 250,
			maxHeight: 600,
		} );

		// 入力項目をクリアする
		clearForm();

		// 初期値を設定する
		$( '[name=id]' ).val( null );
		$( '[name=type]' ).val( 'changelog' );
		setNow( $( '[name=date_time]' ) );

		// 無効化を解除する
		disableFormItem( false );

		// 読み取り専用のコントロールを解除する
		$( '[name=date_time]' ).attr( 'readonly', false );
		$( '[name=title]' ).attr( 'readonly', false );
	} );

	// 削除ボタン - クリックイベント
	$( 'button#delete' ).on( 'click', function() {
		// チェックされたIDリストを作成する
		var ids = $( '[name="select[]"]:checked' ).map( ( index, item ) => $( item ).val() ).get();
		if ( ids.length == 0 ) {
			return;
		}
		if ( ! confirm( __( '選択した項目を削除してもよろしいですか？', 'changelog-extend' ) ) ) {
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
				delete_ids: ids.join(),
			},
		} ).done( function( data, testStatus, xhr ) {
			location.reload();
		} ).fail( function( xhr, testStatus, e ) {
			console.log( 'ajax失敗 : ' + status + xhr );
			console.log( e );
		} );
	} );

	// 編集フォーム - 送信時イベント
	$( '#edit_form' ).submit( function() {
		var result = false;
		if ( ! $( '[name=date_time]' ).val() ) {
			alert( __( '更新日時を指定してください。', 'changelog-extend' ) );
		} else if ( ! $( '[name=title]' ).val() ) {
			alert( __( 'タイトルを入力してください。', 'changelog-extend' ) );
		} else {
			result = confirm( __( '登録してもよろしいですか？', 'changelog-extend' ) );
		}
		return result;
    } );

	// 現在日付を設定する
	function setNow( control ) {
		var date = new Date();
		var dateString = date.getFullYear();
		dateString += '-';
		dateString += zeroPadding( date.getMonth() + 1 );
		dateString += '-';
		dateString += zeroPadding( date.getDate() );
		dateString += 'T';
		dateString += zeroPadding( date.getHours() );
		dateString += ':';
		dateString += zeroPadding( date.getMinutes() );
		dateString += ':';
		dateString += zeroPadding( date.getSeconds() );
		control.val( dateString );
	}

	// ゼロ埋めする
	function zeroPadding( value ) {
		if ( value < 10 ) {
			return '0' + value;
		}
		return value;
	}

	// リストアイテムの有効／無効の設定を行う
	function disableListItem( disableFlag ) {
		$( 'input#hidden_cb' ).prop( 'disabled', disableFlag );
		$( 'select#title_prefix' ).prop( 'disabled', disableFlag );
		$( 'input#title_prefix' ).prop( 'disabled', disableFlag );
		$( 'select#title_suffix' ).prop( 'disabled', disableFlag );
		$( 'input#title_suffix' ).prop( 'disabled', disableFlag );
		$( 'button#edit' ).prop( 'disabled', disableFlag );
	}

	// フォームアイテムの有効／無効の設定を行う
	function disableFormItem( disableFlag ) {
		$.each( controls, function( key, value ) {
			var selector = $( '[name=' + value.name + ']' );
			var tagName = selector.prop( 'tagName' ).toLowerCase();
			if ( tagName == 'input' && selector.attr( 'type' ) == 'hidden' ) {
				return;
			}
			selector.prop( 'disabled', disableFlag );
		} );
		$( '[name=submit]' ).prop( 'disabled', disableFlag );
	}

	// フォームをクリアする
	function clearForm() {
		$.each( controls, function( key, value ) {
			var selector = $( '[name=' + value.name + ']' )
			var tagName = selector.prop( 'tagName' ).toLowerCase();
			if ( tagName == 'input' ) {
				var type = selector.attr( 'type' );
				if ( type == 'hidden' ) {
					return;
				}
				if ( type == 'text' ) {
					selector.val( '' );
				} else if ( type == 'datetime-local' ) {
					selector.val( null );
				} else if ( type == 'checkbox' ) {
					selector.prop( 'checked', false );
				}
			} else if ( tagName == 'select' ) {
				selector.prop( 'selected', false );
			} else if ( tagName == 'textarea' ) {
				selector.val( '' );
			}
		} );

		$( 'select#prefix,select#suffix' ).show();
		$( 'input#prefix,input#suffix' ).hide();
		$( '#prefix_change,#suffix_change' ).removeClass( 'dashicons-list-view' );
		$( '#prefix_change,#suffix_change' ).addClass( 'dashicons-edit' );
	}

	/**
	 * 指定したコンボボックスの値を選択する
	 * @param name セレクタ名
	 * @returns 値
	 */
	function selectComboBoxItem( name, value ) {
		var result = false;
		var selectControl = $( name );
		selectControl.children( 'option:selected' ).prop( 'selected', false );
		selectControl.children( 'option' ).each( ( index, element ) => {
			if ( $( element ).val() == value ) {
				$( element ).prop( 'selected', true );
				result = true;
			}
		});
		return result;
	}

	/** ダイアログを閉じる */
	function closeDialog() {
		$( '#edit_dialog' ).dialog( 'close' );
		$( '#edit_form' ).trigger( 'reset' );
	}
} );
