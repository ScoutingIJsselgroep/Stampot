<!DOCTYPE html>
<html lang="en">
<head><meta content="text/html;charset=utf-8" http-equiv="Content-Type">
	<title>StamPot | Scouting-ijsselgroep.nl</title>
	
	<link rel="shortcut icon" type="image/x-icon" href="favicon.ico" />
	
	<link rel="stylesheet" type="text/css" href="css/Scrollable.css" />
	<link rel="stylesheet" type="text/css" href="css/style.css" />
	
	<script type="text/javascript" src="js/mootools-core-1.4.5.js"></script>
	<script type="text/javascript" src="js/mootools-more-1.4.0.1.js"></script>
	<script type="text/javascript" src="js/Scrollable.js"></script>
	<script type="text/javascript" src="js/LocalStorage.js"></script>
	<script type="text/javascript" src="js/RequestQue.js"></script>
	
	<script type="text/javascript">
	/* <![CDATA[ */
		var active_user;
		
		function filter_users(search) {
			LocalStorage.set('search_user', search);
			
			search = search.toLowerCase();
			document.getElements('.users .list .user').each(function(el) {
				if(active_user) {
					if(el != active_user.div) {
						if(el.getElement('.name').innerHTML.toLowerCase().indexOf(search) > -1) {
							el.setStyle('display','');
						} else {
							el.setStyle('display','none');
						}
					}
				} else {
					if(el.getElement('.name').innerHTML.toLowerCase().indexOf(search) > -1) {
						el.setStyle('display','');
					} else {
						el.setStyle('display','none');
					}
				}
			}, search);
		}
		
		function build_product_list(list) {
			if(list) {
				LocalStorage.set('product_list', list);
			} else {
				list = LocalStorage.get('product_list');
				if(!list) {
					return false;
				}
			}
			document.getElements('.products .list .product').each(function(el) {
				el.dispose();
			});
			Object.each(list, function(product, key) {
				product.price = parseFloat(product.price);
				product.div = new Element('div.product', {
					id: 'product' + key
				}).inject(document.getElement('.products .list'));
				new Element('img', {
					src: (product.image?product.image:'img/product_default.png')
				}).inject(product.div);
				new Element('div.name', {
					html: product.name + ' ('+ product.unit + ')'
				}).inject(product.div);
				new Element('div.price', {
					html: '&euro;' + product.price.toFixed(2).replace('.', ',')
				}).inject(product.div);
				new Element('input.product_price', {
					type: 'hidden',
					value: product.price.toFixed(2)
				}).inject(product.div);
				new Element('input.product_count', {
					type: 'text',
					value: 0,
					size: 3
				}).inject(product.div).addEvents({
					'keyup': function() {
						value = this.value.replace(/[^0-9]/,'').toInt();
						if(isNaN(value)) {
							value = 0;
						}
						this.value = value;
					},
					'change': function() {
						product.div.getElement('.count_price').innerHTML = (product.price * product.div.getElement('.product_count').value.toInt()).format({
						    decimal: ",",
						    group: ".",
						    decimals: 2
						});
					}.bind(product)
				});
				new Element('div.increase', {
					title: '+'
				}).inject(product.div).addEvents({
					'click': function() {
						product.div.getElement('.product_count').value = product.div.getElement('.product_count').value.toInt() + 1;
						product.div.getElement('.product_count').fireEvent('change');
					}.bind(product)
				});
				new Element('div.decrease', {
					title: '-'
				}).inject(product.div).addEvents({
					'click': function() {
						var val = product.div.getElement('.product_count').value.toInt();
						if(val != 0) {
							product.div.getElement('.product_count').value = val - 1;
							product.div.getElement('.product_count').fireEvent('change');
						}
					}.bind(product)
				});
					
				var pay_div = new Element('div.pay', {
					html: '&euro; '
				}).inject(product.div).addEvent('click', function() {
					var amount = product.div.getElement('.product_count').value.toInt();
					if(active_user && amount > 0) {
						request_que.add('users.php?action=buy_product&user_id=' + active_user.id + '&product_id=' + product.id + '&amount=' + amount, 'add_transaction');
					}
					product.div.getElement('.product_count').value = 0;
				}.bind(product));
				new Element('span.count_price', {
					html: '0,00'
				}).inject(pay_div);
				
				product.div.set('Tween', {
					'duration': 500,
					'link': 'cancel'
				});
			});
		}
		
		function add_transaction(transaction) {
			if(active_user && transaction.user_id == active_user.id) {
				active_user.transactions.unshift(transaction);
				active_user.saldo = transaction.saldo_after;
			}
			build_user_detail();
			
			list = LocalStorage.get('user_list');
			Object.each(list, function(user, key) {
				if(user.id == transaction.user_id) {
					user.transactions.unshift(transaction);
					user.saldo = transaction.saldo_after;
					
					div = document.id('user' + key);
					if(div) {
						div.getElement('.saldo').innerHTML = '&euro;' + parseFloat(user.saldo).toFixed(2).replace('.', ',');
					}
				}
			});
			LocalStorage.set('user_list', list);
		}
		
		function build_user_list(list) {
			if(list) {
				LocalStorage.set('user_list', list);
			} else {
				list = LocalStorage.get('user_list');
				if(!list) {
					return false;
				}
			}
			document.getElements('.users .list .user').each(function(el) {
				el.dispose();
			});
			Object.each(list, function(user, key) {
				user.saldo = parseFloat(user.saldo);
				user.div = new Element('div.user', {
					id: 'user' + key
				}).inject(document.getElement('.users .list'));
				new Element('img', {
					src: (user.image?user.image:'img/user_default.png')
				}).inject(user.div);
				new Element('div.name', {
					html: user.name
				}).inject(user.div);
				new Element('div.saldo' + (parseFloat(user.saldo)<parseFloat(user.min_saldo)?'.red':parseFloat(user.saldo)<0?'.orange':''), {
					html: '&euro;' + parseFloat(user.saldo).toFixed(2).replace('.', ',')
				}).inject(user.div);
				new Element('div.pin', {
					title: 'Vastzetten'
				}).inject(user.div);
				
				user.div.set('Tween', {
					'duration': 500,
					'link': 'cancel'
				});
				
				user.div.addEvents({
					'mouseenter': function() {
						this.div.tween('opacity', 1);
						if(active_user && active_user == this) {
							document.getElements('.users .list .user').each(function(el) {
								if(el != this.div) {
									el.tween('opacity', 0.2);
								}
							}, this);
						} else {
							document.getElements('.users .list .user').each(function(el) {
								if(el != this.div) {
									el.tween('opacity', 0.6);
								}
							}, this);
						}
					}.bind(user),
					'click': function() {
						if(active_user && active_user == this) {
							active_user = false;
							document.getElements('.users .list .user').each(function(el) {
								if(el != this.div) {
									el.tween('opacity', 0.6);
								}
							}, this);
						} else {
							active_user = this;
							this.div.tween('opacity', 1);
							document.getElements('.users .list .user').each(function(el) {
								if(el != this.div) {
									el.tween('opacity', 0.2);
								}
							}, this);
						}
						build_user_detail();
					}.bind(user),
					'mouseleave': function() {
						if(active_user) {
							active_user.div.tween('opacity', 1);
							document.getElements('.users .list .user').each(function(el) {
								if(el != active_user.div) {
									el.tween('opacity', 0.2);
								}
							}, this);
						} else {
							document.getElements('.users .list .user').each(function(el) {
								el.tween('opacity', 1);
							});
						}
					}.bind(user)
				});
				
			});
		}
		function build_user_detail() {
			document.getElement('.status .transactions').innerHTML = '';
			if(!active_user) {
				document.getElement('.status #name_input').value = '';
				document.getElement('.status #image_holder img').src = 'img/user_default.png';
				document.getElement('.status .value_saldo').removeClass('red').innerHTML = ' &euro; 0,00';
				document.getElement('.status #saldo_minimal').value = '0,00';
			} else {
				document.getElement('.status #name_input').value = active_user.name;
				document.getElement('.status #image_holder img').src = 'img/user_default.png';
				document.getElement('.status .value_saldo').innerHTML = ' &euro; ' + parseFloat(active_user.saldo).toFixed(2).replace('.', ',');
				if(parseFloat(active_user.saldo)<parseFloat(active_user.min_saldo)) {
					document.getElement('.status .value_saldo').addClass('red');
				} else if(parseFloat(active_user.saldo)<0) {
					document.getElement('.status .value_saldo').addClass('orange');
				}
				
				document.getElement('.status #saldo_minimal').value = parseFloat(active_user.min_saldo).toFixed(2).replace('.', ',');
				
				Object.each(active_user.transactions, function(transaction, key) {
					transaction.div = new Element('div.transaction').inject(document.getElement('.status .transactions'));
					new Element('div.datetime', {
						html: '<span class="date">' + transaction.date.substr(8,2) + '-' + transaction.date.substr(5,2) + '-&#39;' + transaction.date.substr(2,2) + '</span> ' + transaction.date.substr(11,5)
					}).inject(transaction.div);
					new Element('div.amount', {
						html: transaction.amount
					}).inject(transaction.div);
					new Element('div.product', {
						html: transaction.description
					}).inject(transaction.div);
					new Element('div.mutation', {
						html: '&euro; ' + transaction.mutation.replace('.', ',')
					}).inject(transaction.div);
				});
			}
		}
		function build_user_new() {
			document.getElement('.status .transactions').innerHTML = '';
			document.getElement('.status #name_input').value = '';
			document.getElement('.status #image_holder img').src = 'img/user_default.png';
			document.getElement('.status .value_saldo').removeClass('red').innerHTML = ' &euro; 0,00';
			document.getElement('.status #saldo_minimal').value = '0,00';
		}
		function add_user(data) {
			
		}
		function save_user(data) {
			
		}
		
		var request_que;
		window.addEvent('domready', function() {
			request_que = new RequestQue();
			request_que.add('users.php?action=list', 'build_user_list');
			request_que.add('products.php?action=list', 'build_product_list');
			
			
			var search_user = LocalStorage.get('search_user');
			if(search_user) {
				document.id('search_user_input').value = search_user;
				filter_users(search_user);
			}
			
			new Scrollable(document.getElement('.users .list'));
			new Scrollable(document.getElement('.products .list'));
			new Scrollable(document.getElement('.status .transactions'));
			
			document.getElements('.users .list').addEvents({
				'mouseleave': function() {
					if(active_user) {
						new Fx.Scroll(document.getElement('.users .list')).toElementEdge(active_user.div, 'y');
					}
				}
			});
			
			document.id('search_user_input').addEvent('keyup:pause(100)', function(event) {
				filter_users(this.value);
			});
			document.id('search_user_reset').addEvent('click', function(event) {
				document.id('search_user_input').value = '';
				filter_users('');
			});
			document.id('sync').addEvent('click', function(event) {
				request_que.run();
			});
			document.getElement('.user_details .save').addEvent('click', function() {
				if(document.id('name_input').value == '') {
					return;
				}
				min_saldo = document.id('saldo_minimal').value.replace(/[^0-9\,-]/,'').replace(',', '.').toFloat();
				if(isNaN(min_saldo)) {
					min_saldo = 0;
				}
				if(active_user) {
					request_que.add('users.php?action=save&user_id=' + active_user.id + '&name=' + encodeURIComponent(document.id('name_input').value) + '&min_saldo=' + min_saldo, 'save_user');
				} else {
					request_que.add('users.php?action=add&name=' + encodeURIComponent(document.id('name_input').value) + '&min_saldo=' + min_saldo, 'add_user');
				}
			});
			document.id('add_saldo_value').addEvent('change', function() {
				value = this.value.replace(/[^0-9\,-]/,'').replace(',', '.').toFloat();
				if(isNaN(value)) {
					value = 0;
				}
				this.value = value.toFixed(2).replace('.', ',');
			});
			
			document.getElement('.update_saldo .pay').addEvent('click', function() {
				value = document.id('add_saldo_value').value.replace(/[^0-9\,-]/,'').replace(',', '.').toFloat();
				if(isNaN(value)) {
					value = 0;
				}
				
				if(active_user && value != 0) {
					request_que.add('users.php?action=pay&user_id=' + active_user.id + '&description=' + encodeURIComponent(document.id('add_saldo_description').value) + '&amount=' + value, 'add_transaction');
				}
				document.id('add_saldo_value').value = '0,00';
				document.id('add_saldo_description').value = '';
			});
			document.id('add_saldo_value').addEvent('change', function() {
				value = this.value.replace(/[^0-9\,-]/,'').replace(',', '.').toFloat();
				if(isNaN(value)) {
					value = 0;
				}
				this.value = value.toFixed(2).replace('.', ',');
			});
		});
	/* ]]> */
	</script>
</head>
<!--[if lte IE 6 ]><body class="ie6"><![endif]-->
<!--[if gt IE 6 ]><body class="ie"><![endif]-->
<!--[if !(IE)]><!--><body><!--<![endif]-->
	<a href="logout" title="Uitloggen" class="logout">Log uit</a>	
	<span title="Synchroniseren" id="sync" class="sync">Sync</span>
	<div class="floater"></div>
	<div class="total">
		<div class="users">
			<div class="search">
				<input type="text" id="search_user_input" placeholder="Zoek in contactpersonen" />
				<img src="img/funnel_unset.png" id="search_user_reset" alt="" title="reset filter" />
			</div>
			<div class="list">
				
			</div>
			<div class="add user">				
				<img src="img/user_default.png" alt="" />
				<div class="name">
					Nieuwe contactpersoon
				</div>
			</div>
		</div>
		<div class="products">
			<div class="search">
				<input type="text" id="search_product_input" placeholder="Zoek in consumpties" />
				<img src="img/funnel_unset.png" alt="" title="reset filter" />
			</div>
			<div class="add">
				
			</div>
			<div class="list">
				
			</div>
			<div class="pay_free">
				<a href="http://stampot.scouting-ijsselgroep.nl/statistics/">Statistieken</a>
			</div>
		</div>
		<div class="status">
			<div class="user_info">
				<div class="title">
					Details
				</div>
				
				<div class="user_details">
					<input id="name_input" type="text" value="" placeholder="Naam" />
					<label id="image_holder" for="image_input"><img src="img/user_default.png" alt="" /></label>
					
					<input style="width: 0.01px; height: 0.01px;" id="image_input" type="file" />
					
					<img id="image_delete" src="img/bin.png" alt="" title="Afbeelding verwijderen" />
					
					<span class="label_saldo">Saldo</span><span class="value_saldo"> &euro; 0,00</span>
					<label class="label_saldo_minimal" for="saldo_minimal">Minimaal saldo</label> <input id="saldo_minimal" type="text" value="0,00" size="3" />
					
					<span class="save">Opslaan</span>
				</div>
			</div>
			
			<div class="title">
				Laatste transacties
			</div>
			<div class="transactions">
				
			</div>
			
			<div class="update_saldo">
				<div class="name_value saldo_add_value">
					<label for="add_saldo_value">Opwaarderen saldo</label> <input id="add_saldo_value" type="text" value="0,00" size="3" />
				</div>
				<div class="name_value saldo_add_description">
					<label for="add_saldo_description">Beschrijving</label> <input id="add_saldo_description" type="text" value="" maxlength="255" />
				</div>
				
				<div class="pay">Betaal</div>
			</div>
		</div>
	</div>
</body>
</html>
