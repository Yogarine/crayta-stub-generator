--------------------------------------------------------------------------------------------------------
--- @alias StandardAnimationEvent   fun():void
--- @alias BranchingAnimationEvent  fun():boolean
--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------
--- @shape ReloadAnimationEvents
--- @field AmmoAdded        StandardAnimationEvent|nil
--- @field IsReloadComplete BranchingAnimationEvent|nil
--- @field [string] nil
--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------
--- @shape MeleeAnimationEvents
--- @field ChopImpact     StandardAnimationEvent|nil
--- @field IsChopComplete BranchingAnimationEvent|nil
--- @field MeleeImpact    StandardAnimationEvent|nil
--- @field [string] nil
--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------
--- @shape AnimationEvents ReloadAnimationEvents|MeleeAnimationEvents
--------------------------------------------------------------------------------------------------------

--------------------------------------------------------------------------------------------------------
--- @shape AnimationProperties<T : AnimationEvents>
--- @field playbackSpeed  number|nil  @Sets the speed to play this animation at.
---                                   2 = double speed, 0.5 = half speed.
---                                   Note: If playbackTime is also set, you’ll get a warning, and
---                                   playbackTime will be preferred.
--- @field playbackTime   number|nil  @Sets the time that this animation should take.
---                                   1 = 1 second, 10 = 10 seconds.
---                                   Note: If playbackSpeed is also set, you’ll get a warning, and
---                                   playbackTime will be preferred.
--- @field events  T|nil
--------------------------------------------------------------------------------------------------------
local AnimationProperties = {}

return AnimationProperties
