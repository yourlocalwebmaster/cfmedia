<?php
namespace CafeMedia;
use League\Csv\Reader;
use Carbon\Carbon;
use SplFileObject;


class CafeMedia{

    private $csv;
    protected $debug = false;
    public $header,$format;
    //public $top_posts, $other_posts, $daily_top_posts = [];


    public function __construct($path, $format = "csv")
    {
        $this->csv = Reader::createFromPath($path);
        $this->headers = $this->csv->fetchOne(); // save this for the out put CSV
        $this->format = $format;
    }


    /**
     * @return array
     * Return validated (top) posts.
     * --The post must be public
     * --The post must have over 10 comments and over 9000 views
     * --The post title must be under 40 characters
     */
    public function getTopPosts(){

        // init for the halibut.
        $posts = [];

        foreach($this->csv->fetchAssoc() as $row){
            // Does it meet the conditions? If so, send to top post array.
            if($this->validate($row)) $posts[] = $row;
        }

        return $posts;

    }

    /**
     * @return array
     * Return non validated (top) posts.
     */
    public function getOtherPosts(){

        // init for the halibut.
        $posts = [];

        foreach($this->csv->fetchAssoc() as $row){
            // Does it meet the conditions? If so, send to top post array.
            if(!$this->validate($row)) $posts[] = $row;
        }

        return $posts;
    }


    /**
     * @param bool $validate Should the Daily result validate against the same rules that the above exports do?
     * @param null $stringDateOverride Send in an optional date to pull.. i.e. October 5, 2016
     * @return array
     */
    // TODO: Explain why I am ACCEPTING as a valid date, the SAME format you are sending into the CSV
    public function exportDailyTopPost($validate, $today = "Sat Oct 03 04:05:55 2015"){

        // init to win it
        $post = [];

        $placeholder = [];

        $CarbonDesiredDate = $this->timelessTimestamp($today);

        foreach($this->csv->fetchAssoc() as $row){

            // if validate is toggled, don't run if validate fails.
            if($validate && !$this->validate($row)) continue;


            $CarbonCSVDate = $this->timelessTimestamp($row['timestamp']);

            if($CarbonDesiredDate->timestamp == $CarbonCSVDate->timestamp){
                # The below was if we were going to return PAST daily posts. But for sake of speed, I decided to just shoot back the give dates daily post.
                // init to 0 so the likes can override.
                if(!isset($placeholder[$CarbonDesiredDate->timestamp])) $placeholder[$CarbonDesiredDate->timestamp] = 0;

                // if the likes are greater than the existing likes...
                if($placeholder[$CarbonDesiredDate->timestamp] < $row['likes']){
                    $post = $row;
                }

            }

        }

        return [$post];

    }


    /**
     * @param $stringTime
     * @return static
     * Strip the time from the given string time and return it as a Carbon object.
     */
    private function timelessTimestamp($stringTime){
        $time_regex_pattern = '/([0-9]+)\:([0-9]+)\:([0-9]+)/';
        preg_match($time_regex_pattern,$stringTime, $time_matches);
        $formatted_timestamp = str_replace($time_matches[0],'',$stringTime);
        return Carbon::createFromTimestamp(strtotime($formatted_timestamp));
    }

    /**
     * @param $stringDate
     * @return static
     * Takes the malformed timestamp from CSV and converts to Carbon obj.
     */
    private function customDateFormatToCarbon($stringDate){
        $year_regex_pattern = '/([0-9]{4})/';
        $time_regex_pattern = '/([0-9]+)\:([0-9]+)\:([0-9]+)/';
        $month_day_regex_pattern = '/([a-zA-Z]+)\ ([0-9]+)/';

        // format the date into a machine readable format..
        preg_match($year_regex_pattern, $stringDate, $year_matches); // get year. xx:xx:xx
        preg_match($time_regex_pattern, $stringDate, $time_matches); // get time. 2015
        preg_match($month_day_regex_pattern, $stringDate, $month_day_matches); // get Oct & 05
        $month = $month_day_matches[1]; // i.e Oct
        $day = $month_day_matches[2]; // i.e. 05
        $year = $year_matches[1];

        // Alas, we have our Carbon DT obj.
        $DT = Carbon::createFromTimestamp(strtotime($month.' '.$day.', '.$year));
        return $DT;
    }

    /**
     * @param $row
     * @return bool
     * Takes a array, and validates values based on assoc keys to match required scoring spec.
     * --The post must be public
     * --The post must have over 10 comments and over 9000 views
     * --The post title must be under 40 characters*
     */
    private function validate($row){
        return ($row['privacy'] == "public" && $row['comments'] >= 10 && $row['views'] > 9000 && strlen($row['title']) < 40) ? true : false;
    }

    public function generate($data,$name){
        $file = $name.'.'.$this->format;
        if($this->debug) echo "Generating file {$file}<br/>";
        switch($this->format):
            case 'json':
                $json_file = new SplFileObject($file, 'w');
                $json_file->fwrite(json_encode($data));
            break;

            case 'csv':
                $csv_file = new SplFileObject($file, 'w');
                $csv_file->fputcsv($this->headers);
                foreach($data as $row):
                    $csv_file->fputcsv($row);
                endforeach;
            break;
        endswitch;
        if($this->debug) echo "Generated: <a href='lib/{$file}' target='_blank'>{$file}</a><br/>";
    }

    /**
     * @param $url
     * Should redirect user back to a page.
     */
    public function returnTo($url){
        if(!$this->debug) header('Location: '.$url);
    }
}
