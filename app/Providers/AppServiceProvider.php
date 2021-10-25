<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //Paginator::useBootstrapThree();
        Schema::defaultStringLength(191);
        $this->registerValidationRules($this->app['validator']);
    }

    protected function registerValidationRules(\Illuminate\Contracts\Validation\Factory $validator)
    {
        $validator->extend('import_img', function ($attribute, $value, $parameters)
        {
            $tags = explode(', ', $value);
        foreach($tags as $tag)
        {
            // do validation logic
            $extention = explode('.', $tag);
           // echo end($extention);
           // die;
            if (strcasecmp(end($extention), 'jpg') == 0 || strcasecmp(end($extention), 'png') == 0 || strcasecmp(end($extention), 'jpeg') == 0) {
                return true;
            }else{
                return false;
            }
        }
            
            return true;
        });
        $validator->extend('greater_than_field', function($attribute, $value, $parameters, $validator) {
          // print_r($validator->getData());
          // die;
           //  print_r($value);
           //  echo "-";
           //  print_r($parameters[0]);
           //  echo "-";
           // $test =  $validator->getData();
           // print_r($test[2][$parameters[0]]);
           //  die;

            $prices = explode(', ', $value);
            //$saleprice = explode(',', $parameters[0]);
            $min_field = $parameters[0];
            $data = $validator->getData();
            foreach ($data as $key => $datas) {
               $min_value = $datas[$min_field];
            }
           
            foreach ($prices as $key => $price) {
                $min_salary_value = explode(', ', $min_value);
                // print_r($price);
                // echo "-";
                // print_r($min_salary_value[$key]);
                
                
                if($price < $min_salary_value[$key]){
                    return false;
                    //return $price > $min_salary_value[$key];
                }
                // else{
                //     return $price > $min_salary_value[$key];
                // }
               
               // print_r($min_salary_value[1]);
               // $tttt = $min_salary_value;
               // echo "-";
               //return $price > $min_salary_value[$key];
            }
            return true;
          // $min_field = $parameters[0];
          // $data = $validator->getData();
          // $min_value = $data[2][$min_field];
          // return $value > $min_value;
        });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
