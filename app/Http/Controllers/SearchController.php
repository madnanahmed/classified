<?php
namespace App\Http\Controllers;
use App\City;
use App\CustomFieldData;
use Illuminate\Http\Request;
use DB;
use App\Ads;
use App\Category;
use App\Region;
use App\CategoryCustomfields;
use Auth;
class SearchController extends Controller
{
    private $where , $category , $city, $price_range, $price_sort, $keyword = '';
    private $custom_search = false;

    public function search(Request $request)
    {
       
        $result = array();
        $category_id = '';
        $this->keyword = $request->keyword;
        $this->price_sort = $request->price_sort;
        $this->price_range = $request->price_range;

            $this->city = $request->city;

        if ($request->category != '' && is_numeric($request->category)) {
            $category_id = $request->category;
        } else if ($request->main_category != '') {
            //echo 'ok';
            $category_id = Category::where('slug', urldecode($request->main_category) )->value('id');
        }
        $cat = Category::parent($category_id)->renderAsArray();
        $child_ids = Category::childIds($cat);
        array_push($child_ids, $category_id);
        $this->category = $child_ids;
        // custom search
        $totalCf = 0;
        $cf_req_array = array();
        if (is_array($request->custom_search)) {
            $where = '';
            foreach ($request->custom_search as $index => $item) {
                if ($index != '' && $item != '') {
                    $this->custom_search = true;
                    $vl='';
                    foreach($item as $v){
                        $vl.='"'.$v.'",';
                    }
                    $v = rtrim($vl, ',');
                    $where .= '(custom_field_data.column_name = "' . $index . '" and custom_field_data.column_value IN( ' . $v . ') ) OR ';
                    $totalCf++;
                    array_push($cf_req_array, $item);
                }
            }
            $this->where = rtrim(ltrim($where), 'OR ');
        }

        $cfd= CustomFieldData::join('customfields', 'customfields.id', '=', 'custom_field_data.cf_id');
        $cfd = $cfd->where('is_shown', 1);
        if ($this->custom_search == true) {
            $cfd=$cfd->whereRaw($this->where);
        }
        $cfd_ad_id = $cfd->pluck('custom_field_data.ad_id');

        $sql_search = Ads::with(array('city' => function ($query) {
                if ($this->city != '') {
                    $query->where('city.title', $this->city);
                }
            }, 'category' => function ($query) {
                $query->whereIn('categories.id', $this->category);
            }
            , 'save_add' => function ($query) {
                if (!Auth::guest()) {
                    $query->where('save_add.user_id', Auth::user()->id);
                }
            },
                'ad_images', 'city', 'user', 'ad_cf_data'
            )
        );

        if ($this->custom_search == true) {
            $sql_search = $sql_search->whereIn('id', $cfd_ad_id);
        }

        if ($this->city != '') {
            $city_id = City::where('title', $this->city)->value('id');
            $sql_search = $sql_search->where('city_id', $city_id);
        }
        if ($this->category != '') {
            $sql_search = $sql_search->whereIn('category_id', $child_ids);
        }
        // keyword
        if ($request->keyword != '') {
            $sql_search = $sql_search->where('title', 'LIKE', $request->keyword . '%');
        }
        // is image
        if ($request->image != '') {
            $sql_search = $sql_search->where('is_img', $request->image);
        }
        // price sort
        if ($request->price_sort != '') {
            $sql_search = $sql_search->orderBy('price', $request->price_sort);
        }
        // price range
        if ($request->price_range != '') {
            $p_range = explode(';', $request->price_range);
            $sql_search = $sql_search->whereBetween('price', [$p_range[0], $p_range[1]]);
        }
        if ($request->online == 1 && $request->offline != 2) {
            $sql_search = $sql_search->where('is_login', 1);
        } elseif ($request->online == 2 && $request->offline != 1) {
            $sql_search = $sql_search->where('is_login', 0);
        }
        $sql_search = $sql_search->where('status',1);
        $sql_search = $sql_search->orderByRaw("id desc, FIELD(f_type , 'top_page_price', 'urgent_top_price', 'urgent_price','home_page_price', '') ASC");
        $total = $sql_search->count();
        $sql_search = $sql_search
            ->paginate(10)
            ->appends(request()
                ->query());
//        print_r($sql_search);
//
//        exit;
        error_reporting(0);
        if($total > 0){
        if (isset($sql_search)) {
            if ($this->custom_search == true) {
                if (count($sql_search[0]->ad_cf_data) < 1) {
                    //$result = array();
                    $result = $sql_search;
                }else{
                    $result = $sql_search;
                }
            } else {
                $result = $sql_search;
            }
        }
    }

        if($request->has('category')&& $request->category!=''){
            $category = $request->category;
        }else {
            $category = Category::where('slug', $request->main_category)->value('id');
        }
        //extra search
        $search_fields = DB::table('customfields')
            ->join('category_customfields','customfields.id', 'customfields_id')
            ->where(
                [
                    'category_customfields.category_id' => $category,
                    'customfields.search' => 1
                ]
            )
            ->select(
                'customfields.name',
                'customfields.options'
            )
            ->get()->toArray();
        // regions
        $city = City::all();
        // get search fields
        // category
        //$select_category = Category::attr(['name' => 'category', 'class' => 'form-control lselect', 'id' => 'category'])->renderAsDropdown();
        $cat = Category::all()->where('status', 1)->toArray();
        $category = array(
            'categories' => array(),
            'parent_cats' => array()
        );
        //build the array lists with data from the category table
        foreach ($cat as $row) {
            //creates entry into categories array with current category id ie. $categories['categories'][1]
            $category['categories'][$row['id']] = $row;
            //creates entry into parent_cats array. parent_cats array contains a list of all categories with children
            $category['parent_cats'][$row['parent_id']][] = $row['id'];
        }
        $req_category = $category_id;
        return view('search.index', compact('search_fields','result', 'total', 'city', 'category', 'req_category'));
    }
    function ajaxSearch(Request $request){
        $where = '';
        foreach($request->all() as $index => $item){
            $where .= 'column_name = "'.$index.'" and column_value = "'.$item.'" OR ';
        }
        echo  $result = rtrim($where, 'OR ');
    }
}
