<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Crawl;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Storage;
class Crawl extends Controller
{
    public static $BaseUri = "https://www.blocket.se";
    public static $OpenCVMiniService = "http://localhost/miniservice/";

    /**
     * Show the profile for the given user.
     *
     * @param  int  $id
     * @return View
     */
    public static function GetData()
    {
        set_time_limit(3000);
        
        $browser = new HttpBrowser(HttpClient::create());
        for ($page = 1; $page <= 14; $page++) {
            echo "The number is: $page <br>";
        
            if($page == 1){
                $crawler = $browser->request('GET',Crawl::$BaseUri.'/annonser/hela_sverige/fordon/bilar?cg=1020');
            }else{
                $crawler = $browser->request('GET',Crawl::$BaseUri.'/annonser/hela_sverige/fordon/bilar?cg=1020&page='.$page);
            }
            
            
            $cars = Crawl::Crawl($crawler);

            Crawl::SaveToDatabase($cars);
        }
        return ;
    }

    
    /**
     * Show the profile for the given user.
     *
     * @param  string $cars
     * @return int ????
     */
    public static function SaveToDatabase($cars) {
        foreach($cars as $car){
            if($car['brand'] != ""){
                if($car['company']){
                    $car['company'] = "company"; 
                }else{
                    $car['company'] = "private";
                }
                //add cars to database
                DB::table('carinfo_storage')->insert(
                    array(
                        'Brand'     =>   $car['brand'], 
                        'Year'   =>   $car['year'],
                        'LicensePlate' => $car['reg'],
                        'level' => $car['company']
                    )
                );
            }
        }
    }


    /**
     * Go throw each ad
     */
    public static function Crawl($crawler) {
        $cars = $crawler->filter('article')->each(function (Crawler $node, $i) {
            $car = array();

            // Check if its a company
            $company = $node->filter('.AdvertiserTypeSymbol__Badge-sc-54a824-0')->each(function (Crawler $title, $i) {
                return true;
            });

            // Get ad url
            $link = $node->filter('.styled__StyledTitleLink-sc-1kpvi4z-10')->each(function (Crawler $title, $i) {
                return $title->attr('href');
            });    

            $brand = $node->filter('.styled__SubjectContainer-sc-1kpvi4z-11')->each(function (Crawler $title, $i) {
                
                //find brand
                $json = file_get_contents(storage_path('app\brands.json'));//get car brands with json
                $brands = json_decode($json);
                $found = false;//to ignore mutiple matches on one add
                foreach ($brands as $brand) {
                    if(!$found){
                        if (strpos( strtolower($title->text()),  strtolower($brand->name)) !== FALSE) { 
                            if($brand->name != null){
                                return $brand->name;
                            }
                            
                            $found = true;
                        }
                    }
                }
            });
            
            $year = $node->filter('.ParametersList__ListItem-sc-18ndpo4-2')->each(function (Crawler $yearTest, $i) {
                //find year
                $re = '/(19|20)\d\d/';//year regex
                if(preg_match($re,$yearTest->text())){
                    return  $yearTest->text();
                }
            });
            
            if($company != "company"){
                $company = false;
            }

            $reg = Crawl::CrawlAdForLicensePlate($link);

            if($brand != null){
                if($year != null){
                    $brand = implode("|", $brand ) ;
                    $car = array('brand' => $brand, 'year' => $year[0], 'company' => $company,'reg' => $reg);
                    return $car;
                }
            }
        });
        return $cars;
    }

    /**
     * Scanning images for license plate
     *
     * @param  string  $url
     */
    public static function CrawlAdForLicensePlate($url){
        $browser = new HttpBrowser(HttpClient::create());
        
        $crawler = $browser->request('GET',Crawl::$BaseUri.$url[0]);
        $reg = $crawler->filter('#initialState')->each(function (Crawler $node, $i) {
            $json = json_decode($node->html());
            if(isset($json->classified->requestedAd->ad->images)){
                $images = $json->classified->requestedAd->ad->images;
                $found = false;
                foreach ($images as $image) {
                    if($found == false){
                        $img_url = $image->url.'?type=original';
                        $url = Crawl::$OpenCVMiniService;

                        $data = array('img' => $img_url);
                        $options = array(
                            'http' => array(
                                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                                'method'  => 'POST',
                                'content' => http_build_query($data)
                            )
                        );
                        $context  = stream_context_create($options);
                        $result = file_get_contents($url, false, $context);
                        if ($result === FALSE) {
                             // error logs
                         }
                    
                        
                    }
                    return json_decode($result);
                }
            }
            
        });
        if(isset($reg[0]->result)){
            return $reg[0]->result;
        }
        
    }
}
?>
