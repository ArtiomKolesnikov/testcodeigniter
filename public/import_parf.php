<?php

//ini_set('max_execution_time', '3600');
//$host="127.0.0.1";
//$sqlname="";
//$password="";
//$db="";
//$connect=mysql_connect($host,$sqlname,$password) or die(mysql_error());
//mysql_query("CREATE DATABASE IF NOT EXISTS $db;") or die(mysql_error());
//mysql_select_db($db) or die(mysql_error());
//mysql_query("SET NAMES UTF8");
//===============================================================================================================
function transliterate($string) {
    $converter = array(
        "а" => "a",   "б" => "b",   "в" => "v",
        "г" => "g",   "д" => "d",   "е" => "e",
        "ё" => "e",   "ж" => "zh",  "з" => "z",
        "и" => "i",   "й" => "y",   "к" => "k",
        "л" => "l",   "м" => "m",   "н" => "n",
        "о" => "o",   "п" => "p",   "р" => "r",
        "с" => "s",   "т" => "t",   "у" => "u",
        "ф" => "f",   "х" => "h",   "ц" => "c",
        "ч" => "ch",  "ш" => "sh",  "щ" => "sch",
        "ь" => "\"",  "ы" => "y",   "ъ" => "\"",
        "э" => "e",   "ю" => "yu",  "я" => "ya",
        
        "А" => "A",   "Б" => "B",   "В" => "V",
        "Г" => "G",   "Д" => "D",   "Е" => "E",
        "Ё" => "E",   "Ж" => "Zh",  "З" => "Z",
        "И" => "I",   "Й" => "Y",   "К" => "K",
        "Л" => "L",   "М" => "M",   "Н" => "N",
        "О" => "O",   "П" => "P",   "Р" => "R",
        "С" => "S",   "Т" => "T",   "У" => "U",
        "Ф" => "F",   "Х" => "H",   "Ц" => "C",
        "Ч" => "Ch",  "Ш" => "Sh",  "Щ" => "Sch",
        "Ь" => "\"",  "Ы" => "Y",   "Ъ" => "\"",
        "Э" => "E",   "Ю" => "Yu",  "Я" => "Ya",
		" " => "-",	  "&" => "and", "," => "-",
		"." => "-",   "’" => "-",   "'" => "-",   
		"+" => "-",  "№" => "-",  "%" => "-"
    );
    $st = strtr($string, $converter);
	$st = preg_replace("/[^a-zA-ZА-Яа-я0-9\s]/"," ",$st);
	$st = str_replace(" ", "-", $st);
	$st = mb_strtolower($st, "utf-8");
	return $st;
}

$filename1='import.xml';
//$filename2='/var/www/parfumart/data/www/moto.parfumart.ru/import/offers.xml';

if(file_exists($filename1))
{
    $this->load->model('AdditionalOptionSv');
    $this->load->model('OptionsSv');
    $this->load->model('Option');
    $this->load->model('OptionsValue');
    $this->load->model('AdditionalOptionValue');
    $this->load->model('AdditionalGood');
    $this->load->model('Category');
    $this->load->model('Brand');
    $this->load->model('Image');

    AdditionalOptionSv::truncate();
    OptionsSv::truncate();
    Option::truncate();
    OptionsValue::truncate();
    AdditionalOptionValue::truncate();

//    $hide_goods = mysql_query("update parf_goods set `active`='0' where `id`>0");
    $this->load->model('Good');
    Good::where('id','>',0)->update(['active' => 0]);

//	$hide_goods_additional = mysql_query("update parf_additional_goods set `availability`='0' where `id`>0");
    AdditionalGood::where('id','>',0)->update(['availability' => 0]);

//    $hide_categories = mysql_query("update parf_categories set `show`='0' where id>0");
    Category::where('id','>',0)->update(['show' => 0]);

    //===============================================================================================================
	//$hide_goods_additional = mysql_query("update parf_additional_goods set availability='0' where id>0");
	//$hide_goods = mysql_query("update parf_goods set active='0' where id>0");
	/*
	delete from parf_additional_goods;
	delete from parf_additional_option_sv;
	delete from parf_additional_option_value;
	delete from parf_brands;
	delete from parf_categories;
	delete from parf_goods;
	delete from parf_images;
	delete from parf_options;
	delete from parf_options_sv;
	delete from parf_options_value;
	*/
	//7657858578 58 5
			//$value=='import.xml';
	$t1=0;
	$t2=0;		
			//echo $value.' | ';
	//$q101 = mysql_query("delete from parf_additional_option_sv");
	//$q100 = mysql_query("delete from parf_option_sv");	
	//$q102 = mysql_query("delete from parf_options");	
	//$q103 = mysql_query("delete from parf_options_value");	
	//$q104 = mysql_query("delete from parf_additional_option_value");
			
			//файл с категориями, значениями свойств, товарами
			$catalog_tovar = simplexml_load_file("import.xml");//читаем файл

    //$hide_goods = mysql_query("update parf_goods set active='0' where id>0");
			//print_r($tovars);
			//проходимся по элементам]
			
			foreach ($catalog_tovar->Классификатор as $class_t) 
			{
				
				//категории
				foreach ($class_t->Группы->Группа as $group) 
				{

                    $category = Category::where('id_import',$group->Ид)->count();
                    if($category==0)
                    {
                        //$mm=mysql_result(mysql_query("SELECT max(id) from parf_categories"),0,0);
                        //$maxid=$mm+1;
                        if($group->Родитель && Category::where('id_import',$group->Родитель)->count() > 0)
                        {
                            $id_parent = Category::where('id_import',$group->Родитель)->pluck('id')->toArray();
                            if(count($id_parent) == 0)
                            {
                                $id_parent = 0;
                            }
                        $parent_import = $group->Родитель;
                        }
                        else
                        {
                            $id_parent = 0;
                            $parent_import='';
                        }

                        //if($group->Наименование=='Косметика и уход'){$url='cosmetics';}elseif($group->Наименование=='Парфюмерия'){$url='perfume';}else{$url=strtolower(transliterate($group->Наименование));}

                        $url=strtolower(transliterate($group->Наименование));

                        //echo'категория 111<br>';
                        //echo "insert into parf_categories set name='".mysql_escape_string($group->Наименование)."', id_import='".mysql_escape_string($group->Ид)."', rewrite='".mysql_escape_string($url)."', parent_id='".$id_parent."', id_import_parent='".$parent_import."', show='1'<br>";
                        // СОЗДАНИЕ НОВОЙ КАТЕГОРИИ
                        $q2 = Category::create([
                            'name' => $group->Наименование,
                            'id_import' => $group->Ид,
                            'rewrite' => $url,
                            'parent_id' => $id_parent,
                            'id_import_parent' => $parent_import,
                            'show' => 1
                        ]);

//                            $q2 = mysql_query("insert into parf_categories set name='".mysql_escape_string($group->Наименование)."', id_import='".mysql_escape_string($group->Ид)."', rewrite='".mysql_escape_string($url)."', parent_id='".$id_parent."', id_import_parent='".$parent_import."', `show`='1'");
                    }
                    else
                    {
                        if($group->Родитель && Category::where('id_import',$group->Родитель)->count()>0)
                        {
                            $search_category = Category::where('id_import',$group->Родитель)->first();
                            if($search_category)
                            {
                                $id_parent = $search_category->id;
                            }else
                            {
                                $id_parent = 0;
                            }
                            $parent_import=$group->Родитель;
                        }
                        else
                        {
                            $id_parent=0;
                            $parent_import='';
                        }

                        //if($group->Наименование=='Косметика и уход'){$url='cosmetics';}elseif($group->Наименование=='Парфюмерия'){$url='perfume';}else{$url=strtolower(transliterate($group->Наименование));}

                        $url=strtolower(transliterate($group->Наименование));
                        //echo'категория 222<br>';
                        //echo "update parf_categories set name='".mysql_escape_string($group->Наименование)."', rewrite='".mysql_escape_string($url)."', parent_id='".$id_parent."', id_import_parent='".$parent_import."', show='1' where id_import='".mysql_escape_string($group->Ид)."'<br>";

                        //Стало
                        $q2 = Category::where('id_import',$group->Ид)->update([
                            'name' => $group->Наименование,
                            'rewrite' => $url,
                            'parent_id' => $id_parent,
                            'id_import_parent' => $parent_import,
                            'show' => 1
                        ]);

                        //old
//                            $q2 = mysql_query("update parf_categories set name='".mysql_escape_string($group->Наименование)."', rewrite='".mysql_escape_string($url)."', parent_id='".$id_parent."', id_import_parent='".$parent_import."', `show`='1' where id_import='".mysql_escape_string($group->Ид)."'");

                    }

                    //echo $group->Ид.'<br>';
					//echo $group->ПометкаУдаления.'<br>';
					//echo $group->Наименование.'<br><br>';
				}

                //получаем название свойства
				foreach ($class_t->Свойства->Свойство as $sv)
				{

                    //id пол - d59cf8a3-71bd-11e7-8108-87ccb0de009a
					//жен d59cf8a5-71bd-11e7-8108-87ccb0de009a
					//муж d59cf8a4-71bd-11e7-8108-87ccb0de009a
					//унисекс d59cf8a6-71bd-11e7-8108-87ccb0de009a
					if($sv->Наименование!='Производитель' and $sv->Наименование!='Пол')
					{
                        $option = Option::where('id_import',$sv->Ид)->count();
                        //old
//                        $option=mysql_result(mysql_query("SELECT count(id) from parf_options where id_import='".$sv->Ид."'"),0,0);
							if($option==0)
							{
                                $mm_option = Option::max('id');
                                //old
//							$mm_option=mysql_result(mysql_query("SELECT max(id) from parf_options"),0,0);
                                if($mm_option)
                                {
                                    $maxid_option=$mm_option+1;
                                }else
                                {
                                    $maxid_option = 1;
                                }
								if($sv->Наименование=='Раздел'){$sort='1';}
//								elseif($sv->Наименование=='Группа ароматов'){$sort='2';}
								elseif($sv->Наименование=='Категория'){$sort='2';}
//								elseif($sv->Наименование=='Коллекция'){$sort='3';}
								elseif($sv->Наименование=='Серия'){$sort='4';}
								elseif($sv->Наименование=='Проблема'){$sort='5';}
								elseif($sv->Наименование=='Парфюмер'){$sort='6';}
								elseif($sv->Наименование=='Результат'){$sort='6';}
//								elseif($sv->Наименование=='Премьера аромата'){$sort='7';}
//								elseif($sv->Наименование=='Тип Волос'){$sort='7';}
//								elseif($sv->Наименование=='Тип Кожи'){$sort='8';}
//								elseif($sv->Наименование=='Назначение'){$sort='9';}
//								elseif($sv->Наименование=='Верхняя нота'){$sort='10';}
//								elseif($sv->Наименование=='Нота сердца'){$sort='11';}
//								elseif($sv->Наименование=='Базовая нота'){$sort='12';}
								else{$sort='13';}

                                $q3 = Option::create([
                                    'id' => $maxid_option,
                                    'name' => $sv->Наименование,
                                    'id_import' => $sv->Ид,
                                    'sort' => $sort
                                ]);
								//old
//							$q3 = mysql_query("insert into parf_options set id='".$maxid_option."', name='".mysql_escape_string($sv->Наименование)."', id_import='".mysql_escape_string($sv->Ид)."', sort='".$sort."'");
							}
						//echo $sv->Ид.'<br>';
						//echo $sv->ПометкаУдаления.'<br>';
						//echo $sv->Наименование.'<br><br>';
						//получаем значение свойств

                        foreach ($sv->ВариантыЗначений as $sp)
						{
							foreach ($sp->Справочник as $val)
							{
                                $option_sv = OptionsValue::where('id_import',$sv->Ид)->count();
							    //old
//								$option_sv=mysql_result(mysql_query("SELECT count(id) from parf_options_value where id_import='".$sv->Ид."'"),0,0);

									if($option_sv==0)
									{
									    if(!isset($maxid_option))
									    {
									        $maxid_option = 'NULL';
									    }
									//$mm_sv=mysql_result(mysql_query("SELECT max(id) from parf_options_value"),0,0);
									//$maxid_sv=$mm_sv+1; mysql_escape_string(transliterate(strtolower($val_brand->Значение)))
                                        $q4 = OptionsValue::create([
                                            'value' => $val->Значение,
                                            'value_tr' => transliterate(strtolower($val->Значение)),
                                            'id_import' => $val->ИдЗначения,
                                            'id_option' => $maxid_option
                                        ]);
                                        //old
//                                        $q4 = mysql_query("insert into parf_options_value set value='".mysql_escape_string($val->Значение)."', value_tr='".mysql_escape_string(transliterate(strtolower($val->Значение)))."', id_import='".mysql_escape_string($val->ИдЗначения)."', id_option='".$maxid_option."'");
									}
								//echo $val->ИдЗначения.'<br>';
								//echo $val->Значение.'<br><br>';
							}

						}
					}
					elseif($sv->Наименование=='Производитель')
					{

                        //id d59cf8a7-71bd-11e7-8108-87ccb0de009a
						foreach ($sv->ВариантыЗначений as $sp_brand)
						{
							foreach ($sp_brand->Справочник as $val_brand)
							{
                                $option_sv_brand = Brand::where('name',$val_brand->Значение)->count();
                                //old
//                                $option_sv_brand=mysql_result(mysql_query("SELECT count(id) from parf_brands where name='".mysql_escape_string($val_brand->Значение)."'"),0,0);

									if($option_sv_brand==0)
									{
									//$mm_sv=mysql_result(mysql_query("SELECT max(id) from parf_options_value"),0,0);
									//$maxid_sv=$mm_sv+1;
                                        $q4 = Brand::create([
                                            'name' => $val_brand->Значение,
                                            'id_import' => $val_brand->ИдЗначения,
                                            'margin' => 20,
                                            'rewrite' => transliterate(strtolower($val_brand->Значение))
                                        ]);
                                        //old
//									$q4 = mysql_query("insert into parf_brands set name='".mysql_escape_string($val_brand->Значение)."', id_import='".mysql_escape_string($val_brand->ИдЗначения)."', margin='20', rewrite='".mysql_escape_string(transliterate(strtolower($val_brand->Значение)))."'");
									}
									else{

									//$mm_sv=mysql_result(mysql_query("SELECT id from parf_brands where id_import='".mysql_escape_string($val_brand->ИдЗначения)."'"),0,0);
                                        $q4 = Brand::where('name',$val_brand->Значение)->update([
                                            'name' => $val_brand->Значение,
                                            'id_import' => $val_brand->ИдЗначения,
                                            'rewrite' => transliterate(strtolower($val_brand->Значение))
                                        ]);
                                        //old
//									$q4 = mysql_query("update parf_brands set name='".mysql_escape_string($val_brand->Значение)."', id_import='".mysql_escape_string($val_brand->ИдЗначения)."', rewrite='".mysql_escape_string(transliterate(strtolower($val_brand->Значение)))."' where name='".mysql_escape_string($val_brand->Значение)."'");
									}
								//echo $val->ИдЗначения.'<br>';
								//echo $val->Значение.'<br><br>';
							}
						}
					}
				}
					
			}
			//каталог

			foreach ($catalog_tovar->Каталог->Товары as $cat)
			{
				foreach ($cat->Товар as $tovar)
				{
                    $good = Good::where('id_import',$tovar->Ид)->count();
                    //old
//                    $good=mysql_result(mysql_query("SELECT count(id) from parf_goods where id_import='".$tovar->Ид."'"),0,0);

						if($good==0)
						{
                            $mm_tovar = Good::max('id');
						    //old
//						$mm_tovar=mysql_result(mysql_query("SELECT max(id) from parf_goods"),0,0);

						    $maxid_tovar=$mm_tovar+1;

						    $search_category = Category::where('id_import',$tovar->Группы->Ид)->first();
						    if($search_category)
						    {
                                $tovar_category = $search_category->id;
                                $tovar_category_parent = $search_category->parent_id;
                            }else
                            {
                                $tovar_category = 0;
                                $tovar_category_parent = 0;

                            }
						    //old
//						    $tovar_category=mysql_result(mysql_query("SELECT id from parf_categories where id_import='".$tovar->Группы->Ид."'"),0,0);

                            //old
//						$tovar_category_parent=mysql_result(mysql_query("SELECT parent_id from parf_categories where id_import='".$tovar->Группы->Ид."'"),0,0);

                            if($tovar_category_parent > 0)
                            {
                                $category_ids = $tovar_category.'|'.$tovar_category_parent;
                            }else
                            {
                                $category_ids = $tovar_category;
                            }
							//id пол - d59cf8a3-71bd-11e7-8108-87ccb0de009a
							//жен d59cf8a5-71bd-11e7-8108-87ccb0de009a
							//муж d59cf8a4-71bd-11e7-8108-87ccb0de009a
							//унисекс d59cf8a6-71bd-11e7-8108-87ccb0de009a

							//id производитель d59cf8a7-71bd-11e7-8108-87ccb0de009a
							$brand_id = 0;

                            foreach ($tovar->ЗначенияСвойств as $svv_val)
							{
								foreach ($svv_val->ЗначенияСвойства as $sv_val)
								{
									//$brand_id = 0;
									if($sv_val->Ид=='d59cf8a7-71bd-11e7-8108-87ccb0de009a')
									{
									    $search_brand = Brand::where('id_import',$sv_val->Значение)->first();
									    if($search_brand)
									    {
                                            $brand_id = $search_brand->id;
									    }else
                                        {
                                            $brand_id = 0;
                                        }
									    //old
//									    $brand_id=mysql_result(mysql_query("SELECT id from parf_brands where id_import='".$sv_val->Значение."'"),0,0);
//									    if($brand_id==''){$brand_id = 0;}

									}else{/*$brand_id = 0;*/}

									//old
//									if($sv_val->Ид=='d59cf8a3-71bd-11e7-8108-87ccb0de009a')
//									{
//										if($sv_val->Значение=='d59cf8a5-71bd-11e7-8108-87ccb0de009a'){$sex='1';}
//										elseif($sv_val->Значение=='d59cf8a4-71bd-11e7-8108-87ccb0de009a'){$sex='2';}
//										elseif($sv_val->Значение=='d59cf8a6-71bd-11e7-8108-87ccb0de009a'){$sex='3';}
//									}

									if($sv_val->Ид!='d59cf8a7-71bd-11e7-8108-87ccb0de009a' and $sv_val->Ид!='d59cf8a3-71bd-11e7-8108-87ccb0de009a')
									{
									    $search_option = Option::where('id_import',$sv_val->Ид)->first();
									    if($search_option)
									    {
                                            $tovar_id_option = $search_option->id;
									    }else
                                        {
                                            $tovar_id_option = 0;
                                        }
									    //old
//										$tovar_id_option=mysql_result(mysql_query("SELECT id from parf_options where id_import='".$sv_val->Ид."'"),0,0);

                                        $search_option_value = OptionsValue::where('id_import',$sv_val->Значение)->first();
									    if($search_option_value)
									    {
                                            $tovar_id_option_value = $search_option_value->id;
									    }
									    else
                                        {
                                            $tovar_id_option_value = 0;
                                        }
                                        //old
//										$tovar_id_option_value=mysql_result(mysql_query("SELECT id from parf_options_value where id_import='".$sv_val->Значение."'"),0,0);

                                        $q6 = OptionsSv::create([
                                            'id_option' => $tovar_id_option,
                                            'id_value' => $tovar_id_option_value,
                                            'id_good' => $maxid_tovar,
                                            'category_ids' => $category_ids,
                                            'default_category_id' => $tovar_category,
                                            'parent_category' => $tovar_category_parent
                                        ]);
                                        //old
//										$q6 = mysql_query("insert into parf_options_sv set id_option='".$tovar_id_option."', id_value='".$tovar_id_option_value."', id_good='".$maxid_tovar."', category_ids='".$category_ids."', default_category_id='".$tovar_category."', parent_category='".$tovar_category_parent."'");
									}
								}
							}

//todo может быть затык если нет "ПроизводительРус"
                            $q5 = Good::create([
                                'id' => $maxid_tovar,
                                'id_import' => $tovar->Ид,
                                'default_category_id' => $tovar_category,
                                'category_ids' => $category_ids,
                                'brand_id' => $brand_id,
                                'name_1' => $tovar->Наименование,
                                'name_in_cyrillic' => $tovar->ПроизводительРус,
                                'vendor_code' => $tovar->Артикул,
                                'description' => $tovar->Описание,
                                'rewrite' => transliterate(strtolower($tovar->Наименование)),
                                'active' => 1,
                                'parent_category' => $tovar_category_parent
                            ]);
                            //old
//							$q5 = mysql_query("insert into parf_goods set id='".$maxid_tovar."', id_import='".mysql_escape_string($tovar->Ид)."', default_category_id='".$tovar_category."', category_ids='".$category_ids."', brand_id='".$brand_id."', name_1='".mysql_escape_string($tovar->Наименование)."', name_in_cyrillic='".mysql_escape_string($tovar->ПроизводительРус)."', vendor_code='".$tovar->Артикул."', description='".mysql_escape_string($tovar->Описание)."', rewrite='".mysql_escape_string(transliterate(strtolower($tovar->Наименование)))."', active='1', parent_category='".$tovar_category_parent."'");

							$t1=$t1+1;
							echo $t1.' добавлен товар ';

                            $good_img = Image::where([
                                ['good_id',$maxid_tovar],
                                ['import_file',$tovar->Картинка]
                            ])->count();
							//old
//							$good_img=mysql_result(mysql_query("SELECT count(id) from parf_images where good_id='".$maxid_tovar."' and import_file='".mysql_escape_string($tovar->Картинка)."'"),0,0);


							if($good_img == 0 && $tovar->Картинка!='')
							{
								//$q71 = mysql_query("delete from parf_images where good_id='".$maxid_tovar."'");

                                $q51 = Image::create([
                                    'good_id' => $maxid_tovar,
                                    'import_file' => $tovar->Картинка
                                ]);
                                //old
//								$q51 = mysql_query("insert into parf_images set good_id='".$maxid_tovar."', import_file='".mysql_escape_string($tovar->Картинка)."'");
							}

                            $mm_add = AdditionalGood::max('id');
							//old
//							$mm_add=mysql_result(mysql_query("SELECT max(id) from parf_additional_goods"),0,0);

							$maxid_add=$mm_add+1;

                            $q8 = AdditionalGood::create([
                                'id' => $maxid_add,
                                'title' => $tovar->Наименование,
                                'availability' => 1,
                                'good_id' => $maxid_tovar,
                                'price' => $tovar->ЦенаЗаЕдиницу,
                                'id_import' => $tovar->Ид
                            ]);
							//old
//							$q8 = mysql_query("insert into parf_additional_goods set id='".$maxid_add."', title='".mysql_escape_string($tovar->Наименование)."', availability='1', good_id='".$maxid_tovar."', price='".mysql_escape_string($tovar->ЦенаЗаЕдиницу)."', id_import='".mysql_escape_string($tovar->Ид)."'");


							//$t1=$t1+1;
							//echo $t1.' добавлено торговое предложение ';


						}
						else
						{

                            $good_update = (Good::where('id_import',$tovar->Ид)->first())->active;
                            //old
//                            $good_update=mysql_result(mysql_query("SELECT active from parf_goods where id_import='".$tovar->Ид."'"),0,0);

							if($good_update==0)
							{

                                $id_good = (Good::where('id_import',$tovar->Ид)->first())->id;
                                //old
//                                $id_good=mysql_result(mysql_query("SELECT id from parf_goods where id_import='".$tovar->Ид."'"),0,0);

                                $search_category = Category::where('id_import',$tovar->Группы->Ид)->first();

                                $tovar_category = $search_category->id;
                                //old
//                                $tovar_category=mysql_result(mysql_query("SELECT id from parf_categories where id_import='".$tovar->Группы->Ид."'"),0,0);

                                $tovar_category_parent = $search_category->parent_id;
                                //old
//								$tovar_category_parent=mysql_result(mysql_query("SELECT parent_id from parf_categories where id_import='".$tovar->Группы->Ид."'"),0,0);

								if($tovar_category_parent > 0)
								{
								    $category_ids=$tovar_category.'|'.$tovar_category_parent;
								}else
                                {
                                    $category_ids=$tovar_category;
                                }
								/*
								foreach ($tovar->ЗначенияСвойств as $svv_val)
								{
									foreach ($svv_val->ЗначенияСвойства as $sv_val)
									{
										$brand_id = 0;
										if($sv_val->Ид=='d59cf8a7-71bd-11e7-8108-87ccb0de009a')
										{$brand_id=mysql_result(mysql_query("SELECT id from parf_brands where id_import='".$sv_val->Значение."'"),0,0);}
										elseif($sv_val->Ид=='d59cf8a3-71bd-11e7-8108-87ccb0de009a')
										{
											if($sv_val->Значение=='d59cf8a5-71bd-11e7-8108-87ccb0de009a'){$sex='1';}
											elseif($sv_val->Значение=='d59cf8a4-71bd-11e7-8108-87ccb0de009a'){$sex='2';}
											elseif($sv_val->Значение=='d59cf8a6-71bd-11e7-8108-87ccb0de009a'){$sex='3';}
										}
										else
										{
											$tovar_id_option=mysql_result(mysql_query("SELECT id from parf_options where id_import='".$sv_val->Ид."'"),0,0);
											$tovar_id_option_value=mysql_result(mysql_query("SELECT id from parf_options_value where id_import='".$sv_val->Значение."'"),0,0);
											$q7 = mysql_query("delete from parf_options_sv where id_good='".$id_good."'");
											$q6 = mysql_query("insert into parf_options_sv set id_option='".$tovar_id_option."', id_value='".$tovar_id_option_value."', id_good='".$id_good."'");
										}
									}
								}
								*/
								$brand_id = 0;
								foreach ($tovar->ЗначенияСвойств as $svv_val)
								{
									foreach ($svv_val->ЗначенияСвойства as $sv_val)
									{
										//$brand_id = 0;
										if($sv_val->Ид=='d59cf8a7-71bd-11e7-8108-87ccb0de009a')
										{

										    if($search_brand = Brand::where('id_import',$sv_val->Значение)->first())
										    {
                                                $brand_id = $search_brand->id;
										    }else
                                            {
                                                $brand_id = 0;
                                            }
                                            //old
//										    $brand_id=mysql_result(mysql_query("SELECT id from parf_brands where id_import='".$sv_val->Значение."'"),0,0);
//                                            if($brand_id=='')
//										    {
//										        $brand_id = 0;
//										    }

										}else
                                        {/*$brand_id = 0;*/}

//										if($sv_val->Ид=='d59cf8a3-71bd-11e7-8108-87ccb0de009a')
//										{
//											if($sv_val->Значение=='d59cf8a5-71bd-11e7-8108-87ccb0de009a'){$sex='1';}
//											elseif($sv_val->Значение=='d59cf8a4-71bd-11e7-8108-87ccb0de009a'){$sex='2';}
//											elseif($sv_val->Значение=='d59cf8a6-71bd-11e7-8108-87ccb0de009a'){$sex='3';}
//										}

										if($sv_val->Ид!='d59cf8a7-71bd-11e7-8108-87ccb0de009a' and $sv_val->Ид!='d59cf8a3-71bd-11e7-8108-87ccb0de009a')
										{
										    if($search_option = Option::where('id_import',$sv_val->Ид)->first())
										    {
                                                $tovar_id_option = $search_option->id;
										    }else
                                            {
                                                $tovar_id_option = 0;
                                            }
										    //old
//											$tovar_id_option=mysql_result(mysql_query("SELECT id from parf_options where id_import='".$sv_val->Ид."'"),0,0);

                                            if($search_option_value = OptionsValue::where('id_import',$sv_val->Значение)->first())
                                            {
                                                $tovar_id_option_value = $search_option_value->id;
                                            }else
                                            {
                                                $tovar_id_option_value = 0;
                                            }
                                            //old
//											$tovar_id_option_value=mysql_result(mysql_query("SELECT id from parf_options_value where id_import='".$sv_val->Значение."'"),0,0);

                                            $q6 = OptionsSv::create([
                                                'id_option' => $tovar_id_option,
                                                'id_value' => $tovar_id_option_value,
                                                'id_good' => $id_good,
                                                'category_ids' => $category_ids,
                                                'default_category_id' => $tovar_category,
                                                'parent_category' => $tovar_category_parent
                                            ]);
                                            //old
//											$q6 = mysql_query("insert into parf_options_sv set id_option='".$tovar_id_option."', id_value='".$tovar_id_option_value."', id_good='".$id_good."', category_ids='".$category_ids."', default_category_id='".$tovar_category."', parent_category='".$tovar_category_parent."'");
										}
									}
								}



                                $q5 = Good::where('id',$id_good)->update([
                                    'id_import' => $tovar->Ид,
                                    'default_category_id' => $tovar_category,
                                    'category_ids' => $category_ids,
                                    'brand_id' => $brand_id,
                                    'name_1' => $tovar->Наименование,
                                    'name_in_cyrillic' => $tovar->ПроизводительРус,
                                    'vendor_code' => $tovar->Артикул,
                                    'description' => $tovar->Описание,
                                    'rewrite' => transliterate(strtolower($tovar->Наименование)),
                                    'active' => 1,
                                    'parent_category' => $tovar_category_parent
                                ]);
								//old
//								$q5 = mysql_query("update parf_goods set id_import='".mysql_escape_string($tovar->Ид)."', default_category_id='".$tovar_category."', category_ids='".$category_ids."', brand_id='".$brand_id."', name_1='".mysql_escape_string($tovar->Наименование)."', name_in_cyrillic='".mysql_escape_string($tovar->ПроизводительРус)."', vendor_code='".$tovar->Артикул."', description='".mysql_escape_string($tovar->Описание)."', rewrite='".mysql_escape_string(transliterate(strtolower($tovar->Наименование)))."', active='1', parent_category='".$tovar_category_parent."' where id='".$id_good."'");

								$t1=$t1+1;
								echo $t1.' обновлен товар ';

								$good_img = Image::where([
                                    ['good_id',$id_good],
                                    ['import_file',$tovar->Картинка]
                                ])->count();
								//old
//								$good_img=mysql_result(mysql_query("SELECT count(id) from parf_images where good_id='".$id_good."' and import_file='".mysql_escape_string($tovar->Картинка)."'"),0,0);

								if($good_img == 0 && $tovar->Картинка!='')
								{
                                    $q71 = Image::where('good_id',$id_good)->delete();
								    //old
//									$q71 = mysql_query("delete from parf_images where good_id='".$id_good."'");

                                    $q51 = Image::create([
                                        'good_id' => $id_good,
                                        'import_file' => $tovar->Картинка
                                    ]);
                                    //old
//									$q51 = mysql_query("insert into parf_images set good_id='".$id_good."', import_file='".mysql_escape_string($tovar->Картинка)."'");
								}

                                $q8 = AdditionalGood::where('id_import',$tovar->Ид)->update([
                                    'availability' => 1,
                                    'title' => $tovar->Наименование,
                                    'price' => $tovar->ЦенаЗаЕдиницу
                                ]);
                                    //old
//								$q8 = mysql_query("update parf_additional_goods set availability='1', title='".mysql_escape_string($tovar->Наименование)."', price='".$tovar->ЦенаЗаЕдиницу."' where id_import='".mysql_escape_string($tovar->Ид)."'");
								//$t1=$t1+1;
								//echo $t1.' обновлено торговое предложение ';

							}
						}
						//$t2 = $t2 + 1;
						//if($t2 % 500 == 0){sleep(5);}

				//echo $tovar->Ид.'<br>';
				//echo $tovar->Артикул.'<br>';
				//echo $tovar->ПометкаУдаления.'<br>';
				//echo $tovar->Наименование.'<br>';
				//echo $tovar->БазоваяЕдиница['Значение'].''.$tovar->БазоваяЕдиница['НаименованиеПолное'].'<br>';
				//echo $tovar->Группы->Ид.'<br>';
				//echo $tovar->ПроизводительРус.'<br>';
				//echo $tovar->Производитель.'<br>';
				//echo $tovar->Описание.'<br>';
				//echo $tovar->Картинка.'<br><br>';
				}
			}


    dd('!');
//todo сделать удаление
    unlink("/var/www/parfumart/data/www/moto.parfumart.ru/import/import.xml");//удаляем файл
			//unlink("/var/www/parfumart/data/www/shop.parfumart.ru/import/offers.xml");//удаляем файл
			//unlink("/var/www/parfumart/data/www/shop.parfumart.ru/import/Prices.xml");//удаляем файл
			
}	
//===============================================================================================================
//mysql_close();
?>