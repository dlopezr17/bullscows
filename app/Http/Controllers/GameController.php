<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Cache;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Http\Request;
use App\Models\Game;
use DateTime;
use Illuminate\Support\Facades\DB;



class GameController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   /**
* @OA\Get(
*     path="/api/games/all",
*     tags={"Games"},
*     summary="Show all games",
*     @OA\Response(
*         response=200,
*         description="Show all games"
*     ),
*     security = {{"api_key":{}}}
* )
*/
    public function index(Request $request)
    {  
        return response()->json([
            'status' => 'ok',
            'data' => Game::all()
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

     /**
     * @OA\Post(
     ** path="/api/games/store",
     *   tags={"Games"},
     *   summary="Save a new game",
     *   security = {{"api_key":{}}},
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="user",
     *                          type="string"
     *                      ),
     *                       @OA\Property(
     *                          property="age",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "user":"david",
     *                     "age":"36"
     *                }
     *             )
     *         )
     *      ),
     *   @OA\Response(
     *          response=201,
     *          description="CREATED",
     *          @OA\JsonContent(
     *              @OA\Property(property="id", type="number", example=1),
     *              @OA\Property(property="user", type="string", example="david"),
     *              @OA\Property(property="age", type="number", example="36"),
     *              @OA\Property(property="number", type="string", example="1984"),
     *              @OA\Property(property="intents", type="number", example="2"),
     *              @OA\Property(property="time", type="string", example="04:15:02"),
     *              @OA\Property(property="state", type="boolean", example="1")
     *          )
     *      )
     *)
     **/
    public function store(Request $request)
    {
        // if($this->authorizationCheck($request))
        //     return $this->authorizationCheck($request);
        date_default_timezone_set('America/Havana');
        $game = new Game();  
        $json = json_decode($request->getContent(), true); 
        $game->user = $json['user'];
        $game->age = $json['age'];
        $game->number = $this->createNumber(); 
        $game->intents = 0;       
        $game->time = date("H:i:s");  
        $game->state = 0;      
        $game->save();

        $response = [
            'id' => $game->id,
            'user' => $game->user,
            'age' => $game->age,
            'number' => $game->number,
            'intents' => $game->intents,
            'time' => $game->time,
            'state' =>  $game->state 
        ];
        return response()->json([
            'data' => $response
        ], 201);

        
    }



         /**
     * @OA\Post(
     ** path="/api/games/combination",
     *   tags={"Games"},
     *   summary="Send a number to check in play",
     *   security = {{"api_key":{}}},
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                       @OA\Property(
     *                          property="id",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="user",
     *                          type="string"
     *                      ),
     *                      @OA\Property(
     *                          property="combination",
     *                          type="integer"
     *                      )
     *                 ),
     *                 example={
     *                     "id":"12",
     *                     "user":"pepe",
     *                     "combination":"1879"
     *                }
     *             )
     *         )
     *      ),
     *   @OA\Response(
     *          response=200,
     *          description="OK",
     *          @OA\JsonContent(
     *              @OA\Property(property="combination", type="string", example=1948),
     *              @OA\Property(property="evaluation", type="number", example="2"),
     *              @OA\Property(property="ranking", type="number", example="3"),
     *              @OA\Property(property="intents", type="number", example="3"),
     *              @OA\Property(property="availableTime", type="string", example="1984"),
     *              @OA\Property(property="result", type="string", example="2T1V")
     *              
     *          )
     *      ),
     *   @OA\Response(
     *          response=404,
     *          description="Game not found",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="The game not found"),
     *          )
     *      ),
     *   @OA\Response(
     *          response=400,
     *          description="Game ended!",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Game ended!"),
     *          )
     *      ),
     *   @OA\Response(
     *          response=422,
     *          description="Combination invalid",
     *          @OA\JsonContent(
     *              @OA\Property(property="message", type="string", example="Combination invalid"),
     *          )
     *      )
     *  )
     **/

    public function combination(Request $request)
    {
        if($this->authorizationCheck($request))
            return $this->authorizationCheck($request);
        
        date_default_timezone_set('America/Havana'); 
        $json = json_decode($request->getContent(), true);             
        $game = Game::find($json['id']);
        if($game){
            //$json = json_decode($request->getContent(), true); 
            $combination = $json['combination'];
            if(!$this->checkCombination($combination)){
                return response()->json([
                    'code' => 422,
                    'message' => 'Invalid combination'
                ], 422);
            }
            if($game->state == 1){
                Cache::flush();
                return response()->json([
                    'code' => 400,
                    'message' => 'Game ended!'
                ], 400);
            }
            $bulls = 0;
            $cows = 0;
            for($i = 0; $i < strlen($game->number); $i++){            
                for($j = 0; $j < strlen($combination); $j++){
                    $a = $game->number[$i];
                    $b = $combination[$j];
                    if ($a == $b) {
                        if ($i == $j) {
                            $bulls++;
                        } else {
                            $cows++;
                        }
                    }
                } 
            }
    
            $result = $bulls."T".$cows."V";
            $game->intents += 1; 
            $game->save();

            $max_time = env('MAX_TIME');
            // $dateOrigin = new DateTime($game->time);
            // $dateCurrent = new DateTime(date("H:i:s"));
            // $interval = $dateOrigin->diff($dateCurrent);         
            $seconds = strtotime(date("H:i:s")) - strtotime($game->time);
            $availableTime = $max_time - $seconds;   
            $evaluation = $availableTime/2+$game->intents;
            if($game->number == $combination){
                $game->state = 1;
                $game->win = 1;
                $game->evaluation = $evaluation;
                $game->save();
                Cache::flush();
                $ranking = $this->ranking($json['id']);
                $response = [
                    'combination' => $combination,
                    'evaluation' => $evaluation,
                    'ranking' => $ranking,
                    'intents' => $game->intents,
                    'message' => 'Game win!',
                ];
            }
            else if($availableTime > 0){
                $ranking = $this->ranking($json['id']);
                $response = [
                    'combination' => $combination,
                    'evaluation' => $evaluation,
                    'ranking' => $ranking,
                    'intents' => $game->intents,
                    'availableTime' => $availableTime,
                    'result' => $result,
                ];
            }
            else{
                $game->state = 1;
                Cache::flush();
                $game->save();
                $ranking = $this->ranking($json['id']);
                $response = [
                    'combination' => $game->number,
                    'evaluation' => $evaluation,
                    'ranking' => $ranking,
                    'intents' => $game->intents,                   
                    'message' => 'Game over!'
                ];
            }
            if(!$this->storeCombination($combination)){
                return response()->json([
                    'code' => 422,
                    'message' => 'Duplicate Combination'
                ], 422);
            }
            
            return response()->json([
                'status' => 'ok',
                'data' => $response
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Game not found'
            ], 404);
        }   
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @param  string  $user
     * @return \Illuminate\Http\Response
     */

     /**
     * @OA\Post(
     ** path="/api/games/show",
     *   tags={"Games"},
     *   summary="View the info of a game",
     *   security = {{"api_key":{}}},
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="integer"
     *                      ),
     *                      @OA\Property(
     *                          property="user",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "id":"1",
     *                     "user":"david"
     *                }
     *             )
     *         )
     *      ),
     *   @OA\Response(
     *      response=200,
     *       description="Success"
     *   ),
     *   @OA\Response(
     *      response=404,
     *      description="not found"
     *   )
     *   
     *)
     **/
    public function show(Request $request)
    {
        if($this->authorizationCheck($request))
            return $this->authorizationCheck($request);

        $json = json_decode($request->getContent(), true); 
        $id = $json['id'];
        $user = $json['user'];

        $game = Game::find($id);
        if($game){
            return response()->json([
                'status' => 'ok',
                'data' => [
                    'user' => $game->user,
                    'age' => $game->age,
                    'number' => $game->number,
                    'time' => $game->time 
                ]
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Game not found'
            ], 404);
        }        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @param  string  $user
     * @return \Illuminate\Http\Response
     */

    /**
     * @OA\Delete(
     ** path="/api/games/destroy",
     *   tags={"Games"},
     *   summary="Delete a game",
     *   security = {{"api_key":{}}},
     *   @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                      type="object",
     *                      @OA\Property(
     *                          property="id",
     *                          type="number"
     *                      ),
     *                       @OA\Property(
     *                          property="user",
     *                          type="string"
     *                      )
     *                 ),
     *                 example={
     *                     "id":"1",
     *                     "user":"David"
     *                }
     *             )
     *         )
     *      ),
     *   @OA\Response(
     *          response=200,
     *          description="Game deleted",
     *          @OA\JsonContent(
     *               @OA\Property(property="message", type="string", example="Game deleted"),
     *  @OA\Response(
     *      response=404,
     *      description="Game not found"    
     *      )
     *     )
     *    )
     *  )
     **/
    public function destroy(Request $request)
    {
        if($this->authorizationCheck($request))
            return $this->authorizationCheck($request);

        $json = json_decode($request->getContent(), true); 
        $id = $json['id'];

        $game = Game::find($id);  
        if($game) {
            Game::destroy($id);          
            return response()->json([
                'status' => 'Game deleted',
                'data' => ['id' => $id]
            ], 200);
        } else {
            return response()->json([
                'code' => 404,
                'message' => 'Game not found'
            ], 404);
        }        
    }
   
    private function createNumber(){
        $result = [];
                
        while (count($result) < 4) {
            $digit = rand(0, 9);
            if(!in_array($digit, $result)){
                $result[] = $digit;	
            } 
        }               
        
        return implode($result);
    }   
    
    private function checkCombination($number){
        for($i = 0; $i < strlen($number); $i++){ 
            $elem = $number[$i];
            for($j = 0; $j < strlen($number); $j++){                
                $subElem = $number[$j];
                if($i != $j && $elem == $subElem){
                    return false;
                }
            } 
        }
        return true;
    }

    private function storeCombination($number)
    {      
        
        $array = Cache::get('combinations');
        
        if(is_array($array) && array_search($number, $array)!==false)
        {
            return false;
        }
        else
        {
            $array[] = $number;
            Cache::put('combinations', $array);
            $array = Cache::get('combinations');
            return true;
        }

    }

    private function ranking($id)
    {           
        $object = DB::select('select id from games order by win desc, evaluation desc');    
        foreach($object as $value)
            $array[] = $value->id;
            array_unshift($array,"");
            unset($array[0]);

        for($i = 1; $i <= count($array); $i++)
        {                
            
            if($id == $array[$i]){
                return $i;
            }
        }     
        return false;     
        
    }
    
}
