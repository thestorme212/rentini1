<?php

namespace Corp\Http\Controllers\Admin;

use App;
use Gate;
use Corp\Category;
use Corp\Http\Requests\CategoryRequest;
use Corp\Repositories\CategoriesRepository;
use DemeterChain\C;
use Illuminate\Http\Request;

class CategoriesController extends AdminController {
	protected $step;

	public function __construct( CategoriesRepository $c_rep ) {

		parent::__construct();

		//	if(Gate::denies('VIEW_ADMIN')) {
		//	abort(403);
		//}

		$this->c_rep = $c_rep;

		$this->template = 'admins.' . config( 'settings.admin' ) . '.index';

	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index( Category $category ) {
		$this->title = __('admin.Categories');

		$categories =  $category->with('translations','posts',
			'posts.translations')->get()->groupBy( 'parent_id' ) ;




		$arr = buildTree( $categories->toArray(), 0 );

		$form = $this->getTemplate( 'categories.add-category', [ 'categories' => buildSelectOptions( $arr ) ] );

		$content = $this->getTemplate( 'categories.all-categories', [ 'form' => $form, 'categories' => $categories ] );
		$this->vars = array_add( $this->vars, 'content', $content );

		return $this->renderOutput();
		//
	}


	/**
	 * Show the form for creating a new resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function create() {
		//
		$this->title = __('admin.Add New Category');

		$content = $this->getTemplate( 'categories.all-categories' );
		$this->vars = array_add( $this->vars, 'content', $content );

		return $this->renderOutput();

	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store( CategoryRequest $request ) {
		if(Gate::allows( 'ADD_CATEGORY' )){
			//
			return back()->withErrors( [ __( 'You don\'t have permission to add category' ) ] );

		}



		$result = $this->c_rep->addCategory( $request );


		if ( is_array( $result ) && !empty( $result['error'] ) ) {

			return back()->with( $result );
		}

		return back()->with( $result );
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show( $id ) {
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function edit(  $id ) {
		//
	//	dump(\Request::route()->getName() );
		$category = new Category();

		$categories = $this->c_rep->transliterateAll( $category->get()->groupBy( 'parent_id' ) );

		$arr = buildTree( $categories->toArray(), 0 );

		$category = $category->where('id',$id)->first();
		$this->title = __('admin.Edit Category'). ' > '. $category->title;

		$parent_id = $category->parent_id ?? 0;

		$content = $this->getTemplate( 'categories.edit-category',
			[
				'categories' => buildSelectOptions( $arr , $parent_id),
				'category' => $category
				]

		);
		$this->vars = array_add( $this->vars, 'content', $content );

		return $this->renderOutput();
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(CategoryRequest $request,  $id ) {
		//
		//
		if(! Gate::allows( 'UPDATE_CATEGORY' )){
		//
			return back()->withErrors( [ __( 'You don\'t have permission to edit category' ) ] );

		}


		$result = $this->c_rep->updateCategory( $request, $id );


		if ( is_array( $result ) && !empty( $result['error'] ) ) {
			return back()->with( $result );
		}

		return back()->with( $result );
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy( Category $category, Request $request ) {
		//

		if(!Gate::allows( 'DELETE_CATEGORY' )){
			//
			return back()->withErrors( [ __( 'You don\'t have permission to delete category' ) ] );

			abort(403);

		}
		if ( !($request->ajax() && !Gate::allows( 'DELETE_CATEGORY' ))) {
			$result = $this->c_rep->deleteCategory( $category );

			if ( is_array( $result ) && !empty( $result['error'] ) ) {
				if ( $request->ajax() && $request->ajax_load_page ) {
					return json_encode( $request );
				}
				return back()->with( $result );
			}
			if ( $request->ajax() ) {
				return json_encode( $request );
			}
			return redirect( '/admin' )->with( $result );
		}
	}


}
