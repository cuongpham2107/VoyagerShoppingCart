<?php
 
namespace App\Models;
 
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
 
class ProductCategory extends Model
{
  
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    
   /**
    * Get all of the comments for the ProductCategory
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function children(): HasMany
   {
       return $this->hasMany(ProductCategory::class, 'parent_id', 'id')->select('id','name','parent_id','order');
   }
    
}