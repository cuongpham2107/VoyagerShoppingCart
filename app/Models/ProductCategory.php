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

    public $additional_attributes = ['icon_name_category'];

   /**
    * Get all of the comments for the ProductCategory
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
    public function children(): HasMany
    {
        return $this->hasMany(ProductCategory::class, 'parent_id', 'id')->with('parent')->select('id','name','parent_id','order');
    }
     /**
     * Get the user that owns the ProductCategory
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function parent() : BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'parent_id', 'id');
    } 
        
    
    public function getIconNameAttribute()
    {
        return "{$this->icon} {$this->name}";
    }
}  